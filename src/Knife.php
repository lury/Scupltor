<?php
namespace Sculptor;

class Knife {

    public function __construct(\Sculptor\Clay $clay)
    {
        $reader = new \Sabre\Xml\Reader();

        $reader->xml(file_get_contents($clay->input));

        $this->data = $reader->parse();

        foreach($this->data['value'] as $data) {
            foreach($data['value'] as $value) {
                if($value['name'] == "{}item") {
                    $post = $this->addPost($value);
                    $post->write($clay->output);
                }
            }
        }
    }

    public function addPost($postXML) 
    {
        $post = new \Sculptor\Statue;
        foreach($postXML['value'] as $value) {
            if($value['name'] == '{}title') {
                $post->title = $value['value'];
            }  
            if($value['name'] == '{http://purl.org/rss/1.0/modules/content/}encoded') {
                $post_replace_images = preg_replace("/\[caption align=\"align([^\"]+)\" width=\"([^\"]+)\"\]([^[]+)\[\/caption\]/U", '<div style="float: \1; width: \2px;">\3</div>', $value['value']);
                $post->content = $post_replace_images;
            }  
            if($value['name'] == '{http://wordpress.org/export/1.2/}post_date') {
                $post->timestamp = $value['value'];
            } 
            if($value['name'] == '{http://wordpress.org/export/1.2/}post_name') {
                $post->name = $value['value'];
            } 
            //var_dump($value);
            if($value['name'] == '{}category') {
                if($value['attributes']['domain'] == 'category') {
                    $post->categories[] = $value['value'];
                } else {
                    $post->tags[] = $value['value'];
                }
                
            }           
         
         

        }

        return $post;
    }
}
