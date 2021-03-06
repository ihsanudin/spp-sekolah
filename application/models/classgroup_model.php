<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

define('CLASSGROUP_TABLE', 'ar_group_class');

class ClassGroup {
	private $id = NULL;
	private $id_rate = NULL;
	private $id_class = NULL;
	private $status = NULL;
	private $created = NULL;
	private $modified = NULL;
	private $modified_by = NULL;

	public function __construct() {
	}

	public function set_id($id) {
		$this->id = $id;
	}

	public function get_id() {
		return $this->id;
	}

	public function set_id_rate($id_rate) {
		$this->id_rate = $id_rate;
	}

	public function get_id_rate() {
		return $this->id_rate;
	}

	public function set_id_class($id_class) {
		$this->id_class = $id_class;
	}

	public function get_id_class() {
		return $this->id_class;
	}

	public function set_status($status) {
		$this->status = $status;
	}

	public function get_status() {
		return $this->status;
	}

	public function set_created($created) {
		$this->created = $created;
	}

	public function get_created() {
		return $this->created;
	}

	public function set_modified($modified) {
		$this->modified = $modified;
	}

	public function get_modified() {
		return $this->modified;
	}

	public function set_modified_by($modified_by) {
		$this->modified_by = $modified_by;
	}

	public function get_modified_by() {
		return $this->modified_by;
	}

	/**
	 * Method untuk melakukan mapping dari standard object ke classgroup.
	 * Cara yang lebih efektif sebenarnya adalah dengan memodifikasi 
	 * internal Database Driver nya CI.
	 *
	 * @author Rio Astamal 
	 *
	 * @param array $array_of_object - Array hasil query
	 * @return array - Array of $class
	 */
	public static function import_from_array($array_of_object) {
		$objects = array();
		foreach ($array_of_object as $i => $result) {
			$tmp = new ClassGroup();
			$tmp->set_id($result->id);
			$tmp->set_id_rate($result->id_rate);
			$tmp->set_id_class($result->id_class);
			$tmp->set_status($result->status);
			$tmp->set_created($result->created);
			$tmp->set_modified($result->modified);
			$tmp->set_modified_by($result->modified_by);
			$objects[$i] = clone $tmp;
		}
		
		$tmp = NULL;
		
		return $objects;
	}
	
	/**
	 * Method untuk mengexport isi dari object ke dalam type 
	 * associative array atau object.
	 *
	 * @author Rio Astamal 
	 *
	 * @param string $export_type - Tipe data yang akan diexport
	 * @param array $param_exclude - Daftar attribut yang akan diexclude (akan ditambahkan dengan $def_exclude)
	 * @return void
	 */
	public function export($export_type='array', $param_exclude=array()) {
		$result = NULL;
		
		// properti yang akan diexclude dalam hasil
		// sehingga tidak digunakan pada saat akan insert atau update
		$def_exclude = array(
		);
		$exclude = $def_exclude + $param_exclude;
		
		// export dengan tipe array
		if ($export_type === 'array') {
			$result = array();
			foreach ($this as $attr => $val) {
				if (!in_array($attr, $exclude)) {
					// properti tidak termasuk dalam daftar exclude jadi
					// masukkan ke hasil
					$result[$attr] = $val;
				}
			}
			return $result;
		}
		
		// export dengan tipe standard object
		if ($export_type === 'object') {
			$result = new stdClass();
			foreach ($this as $attr => $val) {
				if (!in_array($attr, $exclude)) {
					// properti tidak termasuk dalam daftar exclude jadi
					// masukkan ke hasil
					$result->$attr = $val;
				}
			}
			return $result;
		}
		
		// seharusnya tidak sampai disini jika parameter yang diberikan benar
		throw new Exception('Sepertinya argumen yang anda berikan pada method ClassGroup::export tidak benar.');
	}
}

	
// --- CI Model ---
class ClassGroup_model extends CI_Model {
	public function __construct() {
		parent::__construct();
	}
	
	public function get_both_group($where='', $limit=0, $offset=5) {
		if (empty($where)) $condition1 = $condition2 = '';
		else {
			$condition1 =<<<EOL
and (
	lower(rate.name) like lower('%$where%') 
	OR lower(sis.namalengkap) like lower('%$where%')
	OR lower(kelas.kelas) like lower('%$where%')
	OR lower(kelas.jenjang) like lower('%$where%')
	)
EOL;
			$condition2 =<<<EOL
and (
	lower(rate.name) like lower('%$where%') 
	OR lower(kelas.kelas) like lower('%$where%')
	OR lower(kelas.jenjang) like lower('%$where%')
	)
EOL;
		}

		$query = "
		select 'siswa' kelompok, arg.id, rate.name tagihan, CONCAT(sis.namalengkap, ' - ', kelas.kelas, ' ( ', kelas.jenjang, ' )') peserta
		  from   ar_group_student arg, ar_rate rate, sis_siswa sis, dm_kelas kelas 
			  where arg.id_rate = rate.id and sis.id = arg.id_student and sis.dm_kelas_id = kelas.id $condition1
		";
		$query = $this->db->query($query);
		$r1 = $query->result();

		$query =<<<EOL
select 'kelas' kelompok, arg.id, rate.name tagihan, CONCAT('Kelas ', kelas.kelas, ' ( ', kelas.jenjang, ' )') peserta
	from   ar_group_class arg, ar_rate rate, dm_kelas kelas 
	where arg.id_rate = rate.id and arg.id_class = kelas.id $condition2

EOL;
		$query = $this->db->query($query);
		$r2 = $query->result();

		$result = array_merge($r2, $r1);
		$result = array_slice($result, $limit, $offset);
		return $result;
	}

	public function get_both_group_count($where='', $limit=-1, $offset=0) {
		if (empty($where)) $condition1 = $condition2 = '';
		else {
			$condition1 =<<<EOL
and (
	lower(rate.name) like lower('%$where%') 
	OR lower(sis.namalengkap) like lower('%$where%')
	OR lower(kelas.kelas) like lower('%$where%')
	OR lower(kelas.jenjang) like lower('%$where%')
	)
EOL;
			$condition2 =<<<EOL
and (
	lower(rate.name) like lower('%$where%') 
	OR lower(kelas.kelas) like lower('%$where%')
	OR lower(kelas.jenjang) like lower('%$where%')
	)
EOL;
		}

		$query = "
		select COUNT(-1) as total 
		  from   ar_group_student arg, ar_rate rate, sis_siswa sis, dm_kelas kelas 
			  where arg.id_rate = rate.id and sis.id = arg.id_student and sis.dm_kelas_id = kelas.id $condition1
		";
		$query = $this->db->query($query);
		$r1 = $query->result();

		$query =<<<EOL
select count(-1) total
	from   ar_group_class arg, ar_rate rate, dm_kelas kelas 
	where arg.id_rate = rate.id and arg.id_class = kelas.id $condition2

EOL;
		$query = $this->db->query($query);
		$r2 = $query->result();

		return $r1[0]->total + $r2[0]->total;
	}

	public function get_all_classgroup($where=array(), $limit=-1, $offset=0, $order='') {
		if ($limit > 0) {
			$this->db->limit($limit, $offset);
		}
		if ($where) {
			$this->db->where($where);
		}
		if (! empty($order)) {
			$this->db->order_by($order);
		}
		$query = $this->db->get(CLASSGROUP_TABLE);
		if ($query->num_rows == 0) {
			throw new ClassGroupNotFoundException ("Record tidak ditemukan.");
		}
		
		$result = $query->result();
		
		return ClassGroup::import_from_array($result);
	}
	
	public function get_single_classgroup($where=array()) {
		$record = $this->get_all_classgroup($where, 1, 0);
			
		return $record[0];
	}
	
	public function insert($classgroup) {
		if (FALSE === ($classgroup instanceof ClassGroup)) {
			throw new Exception('Argumen yang diberikan untuk method ClassGroup_model::insert harus berupa instance dari object ClassGroup.');
		}
		
		$data = $classgroup->export();
		$this->db->insert(CLASSGROUP_TABLE, $data);
		
		if ($this->db->affected_rows() == 0) {
			throw new Exception(sprintf('Gagal memasukkan data classgroup.'));
		}

		return $this->db->insert_id();
	}
	
	public function update($classgroup, $exclude=array()) {
		if (FALSE === ($classgroup instanceof ClassGroup)) {
			throw new Exception('Argumen yang diberikan untuk method ClassGroup_model::update harus berupa instance dari object ClassGroup.');
		}
		
		$data = $classgroup->export('array', $exclude);
		$where = array('id' => $classgroup->get_id());
		$this->db->where($where);
		$this->db->update(CLASSGROUP_TABLE, $data);
		
		if ($this->db->affected_rows() == 0) {
			// do nothing
		}
	}
	
	public function custom_update($where, $data) {
		$this->db->where($where);
		$this->db->update(CLASSGROUP_TABLE, $data);
	}
	
	public function delete($classgroup) {
		if (FALSE === ($classgroup instanceof ClassGroup)) {
			throw new Exception('Argumen yang diberikan untuk method classgroup_model::insert harus berupa instance dari object ClassGroup.');
		}
		
		$where = array('id' => $classgroup->get_id());
		$this->db->delete(CLASSGROUP_TABLE, $where);
		
		if ($this->db->affected_rows() == 0) {
			// do nothing
		}

		return $this->db->affected_rows();
	}
	
	public function custom_delete($where) {
		$this->db->delete(CLASSGROUP_TABLE, $where);
	}
}


class ClassGroupNotFoundException extends Exception {
	public function __construct($mesg, $code=101) {
		parent::__construct($mesg, $code);
	}
}
