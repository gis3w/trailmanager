<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Media_Base extends Controller_Ajax_Data_Base{

    protected function _single_request_row($orm) {
        return $this->_get_base_data_from_orm($orm);
        
    }
    
    protected function _get_base_data_from_orm($orm) {
        // si recuperano i media delle due categorie IMAGE e VIDEO
        // caricamento image
        $images = $orm->images->find_all();
        $imagesRes = array();
        foreach($images as $image)
            $imagesRes[] = array(
                'image_url' => $this->_image_uri.$image->file,
                'image_thumb_url' => $this->_image_thumb_uri.$image->file,
                'description' => $image->description
            );
        
        if($this->request->controller() !== 'Itinerary')
        {
            $videos = $orm->videos->find_all();
            $videosRes = array();
            foreach($videos as $video)
                $videosRes[] = array(
                    'title' => $video->title,
                    'video_embed' => $video->embed,
                    'description' => $video->description
                );
        }
        
        $toRes = array(
            'id' => $orm->id,
            'images' => $imagesRes
        );
        
        if($this->request->controller() !== 'Itinerary')
            $toRes['videos'] = $videosRes;
        
        
        return $toRes;
    }
    
   


    
    
}