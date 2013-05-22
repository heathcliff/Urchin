<?php 

class Magazine {
    public static function getLatestIssueImageUri()
    {
        $query = db_select('node', 'n');
        $query->join('field_data_field_image', 'field_image', 'n.nid = field_image.entity_id');
        $query->fields('n', array('nid'))
              ->fields('field_image', array('field_image_fid'))
              ->condition('status', 1)
              ->condition('type', array('issue'))
              ->orderBy('created', 'DESC')
              ->range(0,1);
        $result = reset($query->execute()->fetchAll(PDO::FETCH_ASSOC));
        if (isset($result['field_image_fid']))
        {
            $image = file_load($result['field_image_fid']);
            if (isset($image->uri)) {
                return $image->uri;
            }
        }
        return false;
    }
}