<?php

class Model extends CI_Model

{
	function querydata($query)
	{
		$q=$this->db->query($query);
		return $q->result();
	}

        function querydatar($query)
	{
		$q=$this->db->query($query);
		return $q->row();
	}

	function insert($tb,$dt)
	{
		$this->db->insert($tb,$dt);
		return $this->db->insert_id();
	}

	function update($tb,$dt,$wh)
	{
		$this->db->update($tb,$dt,$wh);	
	}

	function delete($tb,$wh)
	{
		$this->db->delete($tb,$wh);
	}

	function select($tb)
	{
		$q=$this->db->get($tb);
		return $q->result();
	}

	function select_ser($tb,$where)
	{
		$q=$this->db->get_where($tb,$where);
		return $q->result();
	}

	function select_serin($tb,$f,$v)
	{
		$q=$this->db->query("select * from $tb where $f in (0,$v)");
		return $q->result();
	}

	function sel_sum($tb,$f,$where)
	{
		$q=$this->db->query("select sum($f) as total from $tb where $where");
		return $q->row();
	}

	function sel_row($tb,$where)
	{
		$q=$this->db->get_where($tb,$where);
		return $q->row();
	}

	public function record_count($tb,$wh) {
        $this->db->where($wh);
        $this->db->from($tb);
        return $this->db->count_all_results();
    }

    public function record_count_2tbl($tb1,$tb2,$on1,$wh) {
        $this->db->select("*");	
        $this->db->from($tb1);
        $this->db->join($tb2,$on1);
		$this->db->where($wh);	
        return $this->db->count_all_results();
    }

    function sjoin3_row($fields,$tb1,$tb2,$tb3,$on1,$on2,$wh)
	{
		$this->db->select($fields);	
		$this->db->from($tb1);
		$this->db->join($tb2,$on1);
		$this->db->join($tb3,$on2);
		$this->db->where($wh);	
		$q=$this->db->get();
		return $q->result();	
	}

	function sjoin3_row_order($fields,$tb1,$tb2,$tb3,$on1,$on2,$wh,$f)
	{
		$this->db->select($fields);	
		$this->db->from($tb1);
		$this->db->join($tb2,$on1);
		$this->db->join($tb3,$on2);
		$this->db->where($wh);
		$this->db->order_by($f,'desc');	
		$q=$this->db->get();
		return $q->result();	
	}

	function sjoin2_row_left_order($fields,$tb1,$tb2,$on1,$wh,$f,$or)
	{
		$this->db->select($fields);	
		$this->db->from($tb1);
		$this->db->join($tb2,$on1,'left');
		$this->db->where($wh);
		$this->db->order_by($f,$or);	
		$q=$this->db->get();
		return $q->result();	
	}

	function sjoin2_row_order($fields,$tb1,$tb2,$on1,$wh,$f,$or)
	{
		$this->db->select($fields);	
		$this->db->from($tb1);
		$this->db->join($tb2,$on1);
		$this->db->where($wh);
		$this->db->order_by($f,$or);	
		$q=$this->db->get();
		return $q->result();	
	}

	function sjoin2_row($fields,$tb1,$tb2,$on1,$wh)
	{
		$this->db->select($fields);	
		$this->db->from($tb1);
		$this->db->join($tb2,$on1);
		$this->db->where($wh);	
		$q=$this->db->get();
		return $q->row();	
	}

	function sjoin3_rw($fields,$tb1,$tb2,$tb3,$on1,$on2,$wh)
	{
		$this->db->select($fields);	
		$this->db->from($tb1);
		$this->db->join($tb2,$on1);
		$this->db->join($tb3,$on2);
		$this->db->where($wh);	
		$q=$this->db->get();
		return $q->row();	
	}

	function sel_res_or($fields,$tb,$wh,$f,$or)
	{
		$this->db->select($fields);	
		$this->db->from($tb);
		$this->db->where($wh);	
		$this->db->order_by($f,$or);
		$q=$this->db->get();
		return $q->result();	
	}


	public function checkKeyExist($key) 
    {
        try {

            $result =$this->record_count('key_master',array("key_name"=>$key));
            $response = ($result != 0) ? 1 : 0;

        } catch (Exception $e){ exit;} 
        return $response; 
        
    }

    public function checkKeyTokenExist($key, $token)
    {
       try {

        $result =$this->record_count('key_token_master',array("key"=>$key,"token"=>$token));
            $response = ($result != 0) ? 1 : 0;

        } catch (Exception $e){ exit;} 
        return $response; 
    }

    public  function sjoin2_limit_order($fields,$tb1,$tb2,$on1,$wh,$f1,$v1,$f2,$v2,$f,$or,$limit,$start)
	{
		$this->db->select($fields);	
		$this->db->from($tb1);
		$this->db->join($tb2,$on1);
		$this->db->where($wh);
		$this->db->like($f1, $v1);
		$this->db->or_like($f2, $v2);
		$this->db->order_by($f,$or);
		$this->db->limit($limit,$start);	
		$q=$this->db->get();
		return $q->result();	
	}
   

    function sjoin2_limit($fields,$tb1,$tb2,$on1,$wh,$f,$or,$limit,$start)
	{
		$this->db->select($fields);	
		$this->db->from($tb1);
		$this->db->join($tb2,$on1);
		$this->db->where($wh);
		$this->db->order_by($f,$or);
		$this->db->limit($limit,$start);	
		$q=$this->db->get();
		return $q->result();	
	}

    	function sel_fld_row($fields,$tb,$where)
	{
		$this->db->select($fields);	
        $this->db->from($tb);
        $this->db->where($where);
        $q=$this->db->get();	
		return $q->row();
	}

	function sel_fld_lim_res($fields,$tb,$where,$limit,$start)
	{
		$this->db->select($fields);	
        $this->db->from($tb);
        $this->db->where($where);
        $this->db->limit($limit,$start);
        $q=$this->db->get();	
		return $q->result();
	}

	function sel_fld_res($fields,$tb,$where)
	{
		$this->db->select($fields);	
        $this->db->from($tb);
        $this->db->where($where);
        $q=$this->db->get();	
		return $q->result();
	}

	public function updateInc($tbl,$fld,$inc,$where)
	{
		$this->db->where($where);
		$this->db->set($fld, $fld.'+'.$inc, FALSE);
		$this->db->update($tbl);
	}

	public function updateDec($tbl,$fld,$inc,$where)
	{
		$this->db->where($where);
		$this->db->set($fld, $fld.'-'.$inc, FALSE);
		$this->db->update($tbl);
	}




}

?>