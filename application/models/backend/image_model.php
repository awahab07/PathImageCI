<?php
class Image_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function add_image_for_user($user_id=false, $image_data=array(), $image_title='-')
	{
		if(empty($user_id) || empty($image_data))
			return false;
		
		$uploaded_image_file_name = $image_data['file_name'];
		$complete_upload_path = "./uploads/images/$uploaded_image_file_name";
		$datatime = date('Y-m-d H:i:s');

		$inserted = $this->db->insert('images', array(
			'user_id' => $user_id,
			'title' => $image_title,
			'name' => $uploaded_image_file_name,
			'complete_upload_path' => $complete_upload_path,
			'attributes' => json_encode($image_data, JSON_UNESCAPED_SLASHES),
			'date_uploaded' => $datatime,
			'date_modified' =>  $datatime
		));

		return empty($inserted) ? false : $this->db->insert_id();
	}

	public function get_user_images($user_id)
	{
		if(empty($user_id))
			return false;

		$user_images = $this->db->select('i.id, i.title, i.complete_upload_path, l.id as first_layer_id', false)
								->from('images i')
								->join('layers l', 'i.id=l.image_id', 'LEFT')
								->where(array('i.user_id' => $user_id))->get()->result();
		return $user_images;
	}

	public function get_image_row($image_id=false)
	{
		if(empty($image_id))
			return false;

		return $this->db->query("SELECT * FROM images WHERE id='$image_id'")->first_row();
	}

	public function create_layer_for_image($image_id)
	{
		if($this->db->insert('layers', array('image_id' => $image_id, 'name' => 'Annotation Layer', 'date_created' => date('Y-m-d H:i:s'), 'date_modified' => date('Y-m-d H:i:s'))))
			return $this->db->insert_id();
		return false;
	}

	public function get_layer_id_from_feature_id($featrue_id=false)
	{
		if(empty($featrue_id))
			return false;

		return $this->db->query("SELECT layer_id FROM annotations WHERE id='$featrue_id'")->first_row()->layer_id;
	}

	// Returns first layer id found for image id
	public function get_first_layer_id($image_id)
	{
		$layer_row_query = $this->db->select("id")->from("layers")->where(array("image_id" => $image_id))->get();
		if($layer_row_query->num_rows())
			return $layer_row_query->first_row()->id;
		else
			return 0;
	}

	/* Will return string as geojson is not completely json encode and decode able via php */
	public function get_layer_feature_collection_array($image_id, $layer_id)
	{
		if(empty($image_id))
			return false;

		$annotations = false;
		if(empty($layer_id))
		{
			$layer_id_query = $this->db->query("SELECT id FROM layers WHERE image_id=$image_id");
				if($layer_id_query->num_rows())
					$layer_id = $layer_id_query->row()->id;
		}
		
		if(!empty($layer_id))
		{
			$annotation_query = $this->db->query("SELECT * FROM annotations WHERE layer_id=$layer_id");
			if($annotation_query->num_rows())
				$annotations = $annotation_query->result();
		}

		$features_array = array();
		if(!empty($annotations))
		{
			foreach ($annotations as $annotation) {
				$features_array[] = array(
					'geometry' => json_decode($annotation->geometry),
					'type' => $annotation->type,
					'id' => $annotation->id,
					'properties' => json_decode($annotation->properties)
				);
			}
		}
		
		return array(
			'crs' => null,
			'type' => 'FeatureCollection',
			'features' => $features_array
		);
	}

	/* Function to save the JSON features in database and return appropriate response for OpenLayers */
	public function add_layer_features($layer_id, $inputJSONArray)
	{
		if(empty($layer_id) || empty($inputJSONArray))
			return false;

		if(!empty($inputJSONArray['type']) && $inputJSONArray['type'] == 'FeatureCollection')
		{
			foreach ($inputJSONArray['features'] as &$featrue) {
				$row_array = array(
					'layer_id' => $layer_id,
					'geometry' => json_encode($featrue['geometry'], JSON_NUMERIC_CHECK),
					'type' => $featrue['type'],
					'properties' => json_encode($featrue['properties'], JSON_NUMERIC_CHECK),
					'date_created' => date('Y-m-d H:i:s'),
					'date_modified' => date('Y-m-d H:i:s')
				);
				$record_inserted = $this->db->insert('annotations', $row_array);
				if(!empty($record_inserted))
					$featrue['id'] = $this->db->insert_id();
			}

			$inputJSONArray['crs'] = null;
			return $inputJSONArray;
		}

		// In case of no saving
		return array();
	}

	/* Function to update the JSON features in database and return appropriate response for OpenLayers */
	/* Will accept single feature JSON Object and return single feaature JSON object wrapped in feature collection */
	public function update_layer_feature($featrue_id, $inputJSONObject)
	{
		$layer_id = $this->get_layer_id_from_feature_id($featrue_id);
		if(empty($featrue_id))
			return false;

		if(!empty($inputJSONObject['type']))
		{
			$update_array = array(
				'geometry' => json_encode($inputJSONObject['geometry'], JSON_NUMERIC_CHECK),
				'type' => $inputJSONObject['type'],
				'properties' => json_encode($inputJSONObject['properties'], JSON_NUMERIC_CHECK),
				'date_modified' => date('Y-m-d H:i:s')
			);
			$where_array = array('id' => $inputJSONObject['id']);
			$record_updated = $this->db->update('annotations', $update_array, $where_array);
			if(empty($record_updated))
			{
				return false;
			}else{
				return array(
					'crs' => null,
					'type' => 'FeatureCollection',
					'features' => array($inputJSONObject)
				);
			}
		}
		return array();
	}

	/* Function to delete the JSON features in database and return appropriate response for OpenLayers */
	/* Returns an empty feature collection */
	public function delete_layer_feature($featrue_id, $inputJSONObject)
	{
		$layer_id = $this->get_layer_id_from_feature_id($featrue_id);
		if(empty($featrue_id))
			return false;

		$where_array = array('id' => $featrue_id);
		$record_deleted = $this->db->delete('annotations', $where_array);
		if(empty($record_deleted))
		{
			return false;
		}else{
			return array(
				'crs' => null,
				'type' => 'FeatureCollection',
				'features' => null
			);
		}
		return array();
	}	
}

?>