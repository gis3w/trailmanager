__author__ = 'walter'


PG_HOST = 'localhost'
PG_DB = 'dev_trail'
PG_USER = 'postgres'
PG_PASSWORD = 'postgres'

WD = '/home/www/trailmanager/application/cache/'
IMGD = '/home/www/trailmanager/upload/image/'
IMGDTHB = IMGD + 'thumbnail/'

#mapping POI typology => System Typology
#field name => typology id
TYPOLOGY_MAP = {
    'pt_inter': 9,
    'strut_ric': 10,
    'aree_attr':11,
    'insediam': 13,
    'pt_acqua': 8,
    'pt_socc': 12,
    'tipo_segna': 6,
    'stato_segn': 15,
    'fatt_degr': 14,
    'nuov_segna': 16
}

PT_INTER_TYPOLOGY_ORDER  = {
    'pt_inter': 3,
    'strut_ric': 4,
    'aree_attr': 5,
    'insediam': 6,
    'pt_acqua': 2,
    'pt_socc': 1,
}

