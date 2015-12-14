#!/usr/bin/python

__author__ = 'walter'


import psycopg2
import psycopg2.extras
import PIL
from PIL import Image
from settings import *
from bashutils import colors
import os,sys,csv,glob,time, operator
from shutil import copyfile, copy2
import pprint


def main():
    '''
    Main scrit bootstrap function
    '''
    PATH = False
    POIS = False
    SEGMENT_PATHS = False

    pathNumber = sys.argv[1]
    ce = sys.argv[2]
    if len(sys.argv) > 3:
        raise IOError('Only 2 paramaters. If second parament has blanck space, use \' or "')
    WDPath = WD+pathNumber+'/'
    try:

        def getTypologies(rPoi,*keys):
            '''
            Fnction to buil script insert for manytomany typologyes relation in pois
            :return: string
            '''
            qRes = []
            for field in TYPOLOGY_MAP.keys():
                if field != keys[0] and rPoi[field]:
                    qRes.append("INSERT INTO typologies_pois (typology_id,poi_id) VALUES({},{})".format(TYPOLOGY_MAP[field],rPoi['id']))
            return qRes

        #check if directory exixts:

        if not os.path.exists(WDPath):
            raise IOError('Directory {} not exist!'.format(pathNumber))

        #Define our connection string
        conn_string = "host='{}' dbname='{}' user='{}' password='{}'".format(PG_HOST,PG_DB,PG_USER,PG_PASSWORD)

        # print the connection string we will use to connect
        print "Connecting to database\n	->%s" % (conn_string)

        # get a connection, if a connect cannot be made an exception will be raised here
        conn = psycopg2.connect(conn_string)

        # conn.cursor will return a cursor object, you can use this cursor to perform queries
        cursor = conn.cursor(cursor_factory=psycopg2.extras.DictCursor)
        print "Connected!\n"



        #sentieri
        if os.path.exists(WDPath+'sentiero_'+pathNumber+'.shp'):
            #before erase data from db based on name!! is not safe
            qDeletePath = "DELETE FROM paths WHERE nome = '{}'".format(pathNumber)
            cursor.execute(qDeletePath)
            conn.commit()
            PATH = True
            cmdPATH = "ogr2ogr -update -addFields -geomfield 'the_geom' -f PostgreSQL 'PG:dbname={} user={} password={} host={}' {}sentiero_{}.shp -nlt PROMOTE_TO_MULTI -nln public.paths".format(PG_DB,PG_USER,PG_PASSWORD,PG_HOST,WDPath,pathNumber)
            os.system(cmdPATH)
            print colors.color_text("== LOAD PATH SHP DONE! ==", color="green")

        if not PATH:
            return False

        cursor.execute("SELECT se,id from paths ORDER by id DESC limit 1")
        pathRow = cursor.fetchone()
        conn.commit()
        #we get the SE key
        SE = pathRow['se']
        PATH_ID = pathRow['id']




        #punti notevoli
        if os.path.exists(WDPath+'puntinotevoli_'+pathNumber+'.shp'):
            qDeletePoi = "DELETE FROM pois WHERE se = '{}'".format(SE)
            cursor.execute(qDeletePoi)
            conn.commit()
            POIS = True
            cmdPOI = "ogr2ogr -update -addFields -geomfield 'the_geom' -f PostgreSQL 'PG:dbname={} user={} password={} host={}' {}puntinotevoli_{}.shp -nlt POINT -nln public.pois".format(PG_DB,PG_USER,PG_PASSWORD,PG_HOST,WDPath,pathNumber)
            out = os.system(cmdPOI)
            print colors.color_text("== LOAD POI SHP DONE! ==", color="green")


        #tratte
        if os.path.exists(WDPath+'tratte_'+pathNumber+'.shp'):
            qDeleteSegment = "DELETE FROM path_segments WHERE se = '{}'".format(SE)
            cursor.execute(qDeleteSegment)
            conn.commit()
            SEGMENT_PATHS = True
            cmdSEGMENT = "ogr2ogr -update -addFields -geomfield 'the_geom' -f PostgreSQL 'PG:dbname={} user={} password={} host={}' {}tratte_{}.shp -nlt PROMOTE_TO_MULTI -nln public.path_segments".format(PG_DB,PG_USER,PG_PASSWORD,PG_HOST,WDPath,pathNumber)
            os.system(cmdSEGMENT)
            print colors.color_text("== LOAD SEGMENTS SHP DONE! ==", color="green")


        #circuito escursionistico
        qItinerary = "SELECT * FROM itineraries WHERE name='{}'".format(ce)
        cursor.execute(qItinerary)
        recordsItinerary = cursor.fetchone()
        if not recordsItinerary:
            qInsertItinerary = "INSERT INTO itineraries (name,description) VALUES ('{}','')".format(ce)
            cursor.execute(qInsertItinerary)
            cursor.execute(qItinerary)
            recordsItinerary = cursor.fetchone()
        ITINERARY_ID = recordsItinerary['id']


        # COPYNG AND OTHER FOR PATH
        #==================================================
        copyingFieds = [
            "color = '#FF0000'",
            'width = 6',
            'data_ins = {}'.format(int(time.time())),
            'data_mod = data_ins'

        ]
        q = "UPDATE paths SET {} WHERE se = '{}'".format(', '.join(copyingFieds),SE)
        cursor.execute(q)
        print colors.color_text("== COPYING PATH FIELDS DONE! ==", color="green")


        # adding to itinerary PATH
        #-----------------------------------------
        q = "INSERT INTO itineraries_paths (itinerary_id,path_id) VALUES ({},{})".format(ITINERARY_ID,PATH_ID)
        cursor.execute(q)
        print colors.color_text("== ADD PATH TO ITINERARY DONE! ==", color="green")


        # hightlitings path
        #-----------------------------------------
        fileProfile = WDPath+'profile/'+pathNumber+'.csv'
        fieldsProfile = ['x','y','cds2d','z','cds3d','slopdeg']
        #first we delete if exists
        qDelete = "DELETE FROM heights_profile_paths WHERE path_id = {}".format(PATH_ID)
        cursor.execute(qDelete)
        if os.path.exists(fileProfile):
            qMaster = "INSERT INTO heights_profile_paths (path_id,"+','.join(fieldsProfile)+") VALUES ({},{},{},{},{},{},{})"
            with open(fileProfile, 'rb') as csvfile:
                profileReader = csv.DictReader(csvfile)
                for r in profileReader:
                    if r['slopdeg'] == '':
                        r['slopdeg'] = 'null'
                    rValues = [PATH_ID] + [r[k] for k in fieldsProfile]
                    q = qMaster.format(*rValues)
                    cursor.execute(q)
            print colors.color_text("== COPYING PATH HEIGHTS DONE! ==", color="green")


        # COPYNG AND OTHER FOR POI
        #==================================================
        if POIS:
            copyingFieds = [
                # for test
                'data_ins = {}'.format(int(time.time())),
                'data_mod = data_ins'


            ]

            q = "UPDATE pois SET {} WHERE se = '{}'".format(', '.join(copyingFieds),SE)
            cursor.execute(q)
            print colors.color_text("== COPYING POI FIELDS DONE! ==", color="green")


         # adding to itinerary PATH
        #-----------------------------------------

        # adding to itinerary PATH
        #-----------------------------------------
        if POIS:
            q = "SELECT * FROM pois WHERE se='{}'".format(SE)
            cursor.execute(q)
            pois = cursor.fetchall()
            for poi in pois:
                q = "INSERT INTO itineraries_pois (itinerary_id,poi_id) VALUES ({},{})".format(ITINERARY_ID,poi['id'])
                cursor.execute(q)
            print colors.color_text("== ADD POIS TO ITINERARY DONE! ==", color="green")


        # imaging poi
        #-----------------------------------------
        if POIS:
            fieldsImage = ['file','description','data_ins','data_mod','norder']
            fielsImage = glob.glob(WDPath+'Foto/*.*')
            qPois = "SELECT * FROM pois WHERE  se='{}'".format(SE)
            cursor.execute(qPois)
            recordsPois = cursor.fetchall()
            for rPoi in recordsPois:
                if rPoi['photo'] is not None:
                    qImageDelete = "DELETE FROM image_pois WHERE poi_id = {}".format(rPoi['id'])
                    cursor.execute(qImageDelete)
                    #check if files exists
                    jpgFile = WDPath+'Foto/{}.JPG'.format(rPoi['photo'])
                    JPGFile = WDPath+'Foto/{}.jpg'.format(rPoi['photo'])
                    if os.path.exists(jpgFile):
                        fileToCopy = jpgFile
                    elif os.path.exists(JPGFile):
                        fileToCopy = JPGFile
                    else:
                        fileToCopy = None

                    #copy file and inset into db
                    if fileToCopy:
                        newFileName = os.path.basename(fileToCopy).replace('.','_{}.'.format(pathNumber))
                        copy2(fileToCopy, IMGD+newFileName)
                        print colors.color_text("== IMAGE POI {} COPIED! ==".format(newFileName), color="yellow")
                        # resize image for thumbnail
                        dimentionWH = 80
                        img = Image.open(IMGD+newFileName)
                        if(img.size[0] >= img.size[1]):
                            wpercent = (dimentionWH / float(img.size[0]))
                            wsize = dimentionWH
                            hsize = int((float(img.size[1]) * float(wpercent)))
                        else:
                            wpercent = (dimentionWH / float(img.size[1]))
                            wsize = int((float(img.size[0]) * float(wpercent)))
                            hsize = dimentionWH

                        img = img.resize((wsize, hsize), PIL.Image.ANTIALIAS)
                        img.save(IMGDTHB+newFileName)
                        print colors.color_text("== THUMBNAIL IMAGE POI {} CREATED! ==".format(newFileName), color="yellow")

                        now = int(time.time())
                        qImage = "INSERT INTO image_pois (poi_id,"+','.join(fieldsImage)+") VALUES ({},{},{},{},{},{})".format(
                            rPoi['id'],
                            "'{}'".format(newFileName),
                            'null',
                            now,
                            now,
                            0
                        )
                        cursor.execute(qImage)

                #SELECT TYPOLOGY ID
                #-----------------------------
                print rPoi['id']
                if rPoi['fatt_degr'] is not None:
                    print colors.color_text("== DEGRADO! ==", color="red")
                    qPoi = "UPDATE pois SET typology_id={} WHERE id={}".format(TYPOLOGY_MAP['fatt_degr'],rPoi['id'])
                    qTypologiesPoi = getTypologies(rPoi,'fatt_degr')
                elif reduce(operator.or_,[bool(rPoi[k]) for k in PT_INTER_TYPOLOGY_ORDER.keys()]):
                    print colors.color_text("== PT INTERSESSE! ==", color="green")
                    print [bool(rPoi[k]) for k in PT_INTER_TYPOLOGY_ORDER.keys()]
                    #select order:
                    #########################
                    typology_order = 0
                    for k in PT_INTER_TYPOLOGY_ORDER.keys():
                        if rPoi[k] and PT_INTER_TYPOLOGY_ORDER[k] >= typology_order:
                            typology_order = PT_INTER_TYPOLOGY_ORDER[k]
                            typology_id = TYPOLOGY_MAP[k]
                            typology_key = k
                    qPoi = "UPDATE pois SET typology_id={} WHERE id={}".format(typology_id,rPoi['id'])
                    qTypologiesPoi = getTypologies(rPoi,typology_key)
                else:
                    print colors.color_text("== SEGNALETICA! ==", color="green")
                    if rPoi['nuov_segna']:
                        qPoi = "UPDATE pois SET typology_id={}, publish=false WHERE id={}".format(TYPOLOGY_MAP['nuov_segna'],rPoi['id'])
                        qTypologiesPoi = getTypologies(rPoi,'nuov_segna')
                    elif rPoi['stato_segn']:
                        qPoi = "UPDATE pois SET typology_id={}, publish=false WHERE id={}".format(TYPOLOGY_MAP['stato_segn'],rPoi['id'])
                        qTypologiesPoi = getTypologies(rPoi,'stato_segn')
                    else:
                        qPoi = "UPDATE pois SET typology_id={} WHERE id={}".format(TYPOLOGY_MAP['tipo_segna'],rPoi['id'])
                        qTypologiesPoi = getTypologies(rPoi,'tipo_segna')
                cursor.execute(qPoi)
                for qtp in qTypologiesPoi:
                    cursor.execute(qtp)
            print colors.color_text("== COPYING AND INSERTING POI IMAGES DONE! ==", color="green")



        conn.commit()
        conn.close()


    except:
        print "Unexpected error:", sys.exc_info()[0]
        raise


if __name__ == "__main__":
    main()