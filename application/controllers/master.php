<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Master extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('Mod_master');
		session_start();
	}
	
	public function index()
	{
		if ( isset($_SESSION['username']) == TRUE )
		{ 
			$siapa=$_SESSION['username'];
			if ($siapa == 'admin')
			{
				$tot = $this->Mod_master->Total('pl','data_user','state');
				$data['totbidang'] = $this->Mod_master->Totalbid('2');
				$q = $this->Mod_master->Ambil('tutup','pesan','id','pengumuman');
				foreach($q->result_array() as $t){ $tutup = $t['pesan']; }
				$data['tutup'] = $tutup;
				$totn = $this->Mod_master->Total('2','data_user','id_bidang');
				$data["bidang"]=$this->Mod_utama->Bidang();
				$data['total'] = $tot->num_rows();
				$data['totaln'] = $totn->num_rows();
				$data['scriptmce'] = $this->scripttiny_mce();
				$this->load->view('master/bg_atas', $data);
				$this->load->view('master/isi', $data);
				$this->load->view('master/bg_bawah');
			}
			else
			{
				?>
				<script type="text/javascript" language="javascript">
				alert("Anda tidak berhak masuk ke Control Panel Admin...!!!");
				window.location = "<?php echo base_url(); ?>index.php"
				</script>
				<?php
			}
		}
		else
		{
			?>
			<script type="text/javascript" language="javascript">
			alert("Anda harus login...!");
			window.location = "<?php echo base_url(); ?>index.php"
			</script>
			<?php
		}
	}
	
	function keluar() 
    {
            session_destroy(); 
			echo "<meta http-equiv='refresh' content='0; url=".base_url()."index.php'>";

    }
	
	function tutup() 
    {
	
		$q = $this->Mod_master->Ambil('tutup','pesan','id','pengumuman');
		foreach($q->result_array() as $t){ $tutup = $t['pesan']; }
		if ($tutup === '1'){$d['pesan'] = '0';} else {$d['pesan'] = '1';}
        	$this->db->trans_start();
			$this->db->where('id','tutup');
			$this->db->update('pengumuman',$d);
			$this->db->trans_complete();
			?>
			<script type="text/javascript">
			javascript:history.go(-1);
			</script>
			<?php

    }
	
	function cetaksemua()
	{
		if ( isset($_SESSION['username']) == TRUE )
		{ 
			$siapa=$_SESSION['username'];
			if ($siapa == 'admin')
			{
				$page=$this->uri->segment(3);
				$data["pl"] = $this->Mod_master->Ambil($page,'*','id_bidang','data_user');
				$data['scriptmce'] = $this->scripttiny_mce();
			//	$this->load->view('master/bg_atas', $data);
				$this->load->view('master/cetak', $data);
			//	$this->load->view('master/bg_bawah');
			}
			else
			{
				?>
				<script type="text/javascript" language="javascript">
				alert("Anda tidak berhak masuk ke Control Panel Admin...!!!");
				window.location = "<?php echo base_url(); ?>index.php"
				</script>
				<?php
			}
		}
		else
		{
			?>
			<script type="text/javascript" language="javascript">
			alert("Anda harus login...!");
			window.location = "<?php echo base_url(); ?>index.php"
			</script>
			<?php
		}
	}
	
	function rekap()
	{
		if ( isset($_SESSION['username']) == TRUE )
		{ 
			$siapa=$_SESSION['username'];
			if ($siapa == 'admin')
			{
				$page=$this->uri->segment(3);
				$limit=10;
				if(!$page):
				$offset = 0;
				else:
				$offset = $page;
				endif;
				$data["pl"] = $this->Mod_master->Daftarpl($offset,$limit);
				$data["bids"] = $this->Mod_utama->Bidang();
				$tot = $this->Mod_master->Total('pl','data_user','state');
				$config['base_url'] = base_url() . '/index.php/master/rekap/';
				$config['total_rows'] = $tot->num_rows();
				$config['per_page'] = $limit;
				$config['uri_segment'] = 3;
				$config['first_link'] = 'Awal';
				$config['last_link'] = 'Akhir';
				$config['next_link'] = 'Selanjutnya';
				$config['prev_link'] = 'Sebelumnya';
				$this->pagination->initialize($config);
				$data["paginator"]=$this->pagination->create_links();
				$data["page"] = $page;
				$data['scriptmce'] = $this->scripttiny_mce();
				$this->load->view('master/bg_atas', $data);
				$this->load->view('master/rekap', $data);
				$this->load->view('master/bg_bawah');
			}
			else
			{
				?>
				<script type="text/javascript" language="javascript">
				alert("Anda tidak berhak masuk ke Control Panel Admin...!!!");
				window.location = "<?php echo base_url(); ?>index.php"
				</script>
				<?php
			}
		}
		else
		{
			?>
			<script type="text/javascript" language="javascript">
			alert("Anda harus login...!");
			window.location = "<?php echo base_url(); ?>index.php"
			</script>
			<?php
		}
	}
	

	function simpan()
	{
		if ( isset($_SESSION['username']) == TRUE )
		{ 
			$siapa=$_SESSION['username'];
			if ($siapa == 'admin')
			{
			$in["pesan"]=$this->input->post('lulus');
			$in2["pesan"]=$this->input->post('tidaklulus');
			$in3["pesan"]=$this->input->post('umum');
			$this->db->trans_start();
			$this->db->where('id','Lulus');
			$this->db->update('pengumuman',$in);
			$this->db->trans_complete();
			$this->db->trans_start();
			$this->db->where('id','Tidak Lulus');
			$this->db->update('pengumuman',$in2);
			$this->db->trans_complete();
			$this->db->trans_start();
			$this->db->where('id','Umum');
			$this->db->update('pengumuman',$in3);
			$this->db->trans_complete();
			if ($this->db->trans_status() === FALSE)
				{
					?><script type="text/javascript" language="javascript">
					alert("ada yang SALAH...! Silahkan ulangi...");
					javascript:history.go(-1);
					</script><?php
				}
				else
				{ 
					?><script type="text/javascript" language="javascript">
					javascript:history.go(-1);
					</script><?php
			}
			}
			else
			{
				?>
				<script type="text/javascript" language="javascript">
				alert("Anda tidak berhak masuk ke Control Panel Admin...!!!");
				window.location = "<?php echo base_url(); ?>index.php"
				</script>
				<?php
			}
		}
		else
		{
			?>
			<script type="text/javascript" language="javascript">
			alert("Anda harus login...!");
			window.location = "<?php echo base_url(); ?>index.php"
			</script>
			<?php
		}
	}	
	
	function simpanbid()
	{
		if ( isset($_SESSION['username']) == TRUE )
		{ 
			$siapa=$_SESSION['username'];
			if ($siapa == 'admin')
			{
			$d["nama_bidang"]=$this->input->post('bidang');
			$d["rincian"]=$this->input->post('rincian');
			$this->db->trans_start();
			$this->db->insert('data_bidang',$d);
			$this->db->trans_complete();
			if ($this->db->trans_status() === FALSE)
				{
					?><script type="text/javascript" language="javascript">
					alert("ada yang SALAH...! Silahkan ulangi...");
					javascript:history.go(-1);
					</script><?php
				}
				else
				{ 
					?><script type="text/javascript" language="javascript">
					javascript:history.go(-1);
					</script><?php
			}
			}
			else
			{
				?>
				<script type="text/javascript" language="javascript">
				alert("Anda tidak berhak masuk ke Control Panel Admin...!!!");
				window.location = "<?php echo base_url(); ?>index.php"
				</script>
				<?php
			}
		}
		else
		{
			?>
			<script type="text/javascript" language="javascript">
			alert("Anda harus login...!");
			window.location = "<?php echo base_url(); ?>index.php"
			</script>
			<?php
		}
	}
	
	function penilaian()
	{
		if ( isset($_SESSION['username']) == TRUE )
		{ 
			$siapa=$_SESSION['username'];
			if ($siapa == 'admin')
			{
				$page=$this->uri->segment(3);
				$limit=10;
				if(!$page):
				$offset = 0;
				else:
				$offset = $page;
				endif;
				$data["pl"] = $this->Mod_master->Daftarpl($offset,$limit);
				$tot = $this->Mod_master->Total('pl','data_user','state');
				$config['base_url'] = base_url() . '/index.php/master/penilaian/';
				$config['total_rows'] = $tot->num_rows();
				$config['per_page'] = $limit;
				$config['uri_segment'] = 3;
				$config['first_link'] = 'Awal';
				$config['last_link'] = 'Akhir';
				$config['next_link'] = 'Selanjutnya';
				$config['prev_link'] = 'Sebelumnya';
				$this->pagination->initialize($config);
				$data["paginator"]=$this->pagination->create_links();
				$data["page"] = $page;
				$data['scriptmce'] = $this->scripttiny_mce();
				$this->load->view('master/bg_atas', $data);
				$this->load->view('master/hasil', $data);
				$this->load->view('master/bg_bawah');
			}
			else
			{
				?>
				<script type="text/javascript" language="javascript">
				alert("Anda tidak berhak masuk ke Control Panel Admin...!!!");
				window.location = "<?php echo base_url(); ?>index.php"
				</script>
				<?php
			}
		}
		else
		{
			?>
			<script type="text/javascript" language="javascript">
			alert("Anda harus login...!");
			window.location = "<?php echo base_url(); ?>index.php"
			</script>
			<?php
		}
	}
	
		private function scripttiny_mce() {
		return '
		<!-- TinyMCE -->
		<script type="text/javascript" src="'.base_url().'jscripts/tiny_mce/tiny_mce_src.js"></script>
		<script type="text/javascript">
		tinyMCE.init({
		// General options
		mode : "textareas",
		theme : "advanced",
		plugins : "pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount,advlist,autosave",

		// Theme options
		theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
		theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
		theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
		theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak,restoredraft",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,

		// Example content CSS (should be your site CSS)
		content_css : "'.base_url().'system/application/views/themes/css/BrightSide.css",

		// Drop lists for link/image/media/template dialogs
		//"'.base_url().'media/lists/image_list.js"
		template_external_list_url : "lists/template_list.js",
		external_link_list_url : "lists/link_list.js",
		external_image_list_url : "'.base_url().'index.php/adminweb/image_list/",
		media_external_list_url : "lists/media_list.js",

		// Style formats
		style_formats : [
			{title : \'Bold text\', inline : \'b\'},
			{title : \'Red text\', inline : \'span\', styles : {color : \'#ff0000\'}},
			{title : \'Red header\', block : \'h1\', styles : {color : \'#ff0000\'}},
			{title : \'Example 1\', inline : \'span\', classes : \'example1\'},
			{title : \'Example 2\', inline : \'span\', classes : \'example2\'},
			{title : \'Table styles\'},
			{title : \'Table row 1\', selector : \'tr\', classes : \'tablerow1\'}
		],

		// Replace values for the template plugin
		template_replace_values : {
			username : "Some User",
			staffid : "991234"
		}
	});
</script>';	
	}
	
	function pengumuman()
	{
		if ( isset($_SESSION['username']) == TRUE )
		{ 
			$siapa=$_SESSION['username'];
			if ($siapa == 'admin')
			{
				$tot = $this->Mod_master->Total('pl','data_user','state');
				$data['total'] = $tot->num_rows();
				$data['scriptmce'] = $this->scripttiny_mce();
				$data["umum"] = $this->Mod_utama->Pengumuman('Umum');
				$data["lulus"] = $this->Mod_utama->Pengumuman('Lulus');
				$data["tidaklulus"] = $this->Mod_utama->Pengumuman('Tidak Lulus');
				$this->load->view('master/bg_atas', $data);
				$this->load->view('master/pengumuman', $data);
				$this->load->view('master/bg_bawah');
			}
			else
			{
				?>
				<script type="text/javascript" language="javascript">
				alert("Anda tidak berhak masuk ke Control Panel Admin...!!!");
				window.location = "<?php echo base_url(); ?>index.php"
				</script>
				<?php
			}
		}
		else
		{
			?>
			<script type="text/javascript" language="javascript">
			alert("Anda harus login...!");
			window.location = "<?php echo base_url(); ?>index.php"
			</script>
			<?php
		}
	}
	
	function tambahbid()
	{
		if ( isset($_SESSION['username']) == TRUE )
		{ 
			$siapa=$_SESSION['username'];
			if ($siapa == 'admin')
			{
				$data['scriptmce'] = $this->scripttiny_mce();
				$this->load->view('master/bg_atas', $data);
				$this->load->view('master/tambahbid');
				$this->load->view('master/bg_bawah');
			}
			else
			{
				?>
				<script type="text/javascript" language="javascript">
				alert("Anda tidak berhak masuk ke Control Panel Admin...!!!");
				window.location = "<?php echo base_url(); ?>index.php"
				</script>
				<?php
			}
		}
		else
		{
			?>
			<script type="text/javascript" language="javascript">
			alert("Anda harus login...!");
			window.location = "<?php echo base_url(); ?>index.php"
			</script>
			<?php
		}
	}


	function hapus()
	{
		if ( isset($_SESSION['username']) == TRUE )
		{ 
			$siapa=$_SESSION['username'];
			if ($siapa == 'admin')
			{
				$d = $this->uri->segment(3);
				$id = $d."/REK/2013";
				$this->Mod_master->Hapus_Sesuatu($id, 'no_reg', 'data_user');
				?><script type="text/javascript" language="javascript">
					javascript:history.go(-1);
					</script><?php
			}
			else
			{
				?>
				<script type="text/javascript" language="javascript">
				alert("Anda tidak berhak masuk ke Control Panel Admin...!!!");
				window.location = "<?php echo base_url(); ?>index.php"
				</script>
				<?php
			}
		}
		else
		{
			?>
			<script type="text/javascript" language="javascript">
			alert("Anda harus login...!");
			window.location = "<?php echo base_url(); ?>index.php"
			</script>
			<?php
		}
	}


	function hapusbid()
	{
		if ( isset($_SESSION['username']) == TRUE )
		{ 
			$siapa=$_SESSION['username'];
			if ($siapa == 'admin')
			{
				$id = $this->uri->segment(3);
				$this->Mod_master->Hapus_Sesuatu($id, 'id_bidang', 'data_bidang');
				?><script type="text/javascript" language="javascript">
					javascript:history.go(-1);
					</script><?php
			}
			else
			{
				?>
				<script type="text/javascript" language="javascript">
				alert("Anda tidak berhak masuk ke Control Panel Admin...!!!");
				window.location = "<?php echo base_url(); ?>index.php"
				</script>
				<?php
			}
		}
		else
		{
			?>
			<script type="text/javascript" language="javascript">
			alert("Anda harus login...!");
			window.location = "<?php echo base_url(); ?>index.php"
			</script>
			<?php
		}
	}
		
	function kontak()
	{
		if ( isset($_SESSION['username']) == TRUE )
		{ 
			$siapa=$_SESSION['username'];
			if ($siapa == 'admin')
			{

				$data['scriptmce'] = $this->scripttiny_mce();
				$data["kontak"] = $this->Mod_master->Kontak();
				$this->load->view('master/bg_atas', $data);
				$this->load->view('master/kontak', $data);
				$this->load->view('master/bg_bawah');
			}
			else
			{
				?>
				<script type="text/javascript" language="javascript">
				alert("Anda tidak berhak masuk ke Control Panel Admin...!!!");
				window.location = "<?php echo base_url(); ?>index.php"
				</script>
				<?php
			}
		}
		else
		{
			?>
			<script type="text/javascript" language="javascript">
			alert("Anda harus login...!");
			window.location = "<?php echo base_url(); ?>index.php"
			</script>
			<?php
		}
	}
	
	function lulus()
	{
		$id='';
		if ($this->uri->segment(3) === FALSE)
			{
    			$id=$id;
			}
			else
			{
    			$d = $this->uri->segment(3);
				$id = $d."/REK/2013";
			}
		$q = $this->Mod_master->Ambil($id,"hasil","no_reg","data_user");
		foreach ($q->result() as $a)
		$hasil=$a->hasil;
		if ($hasil === 'Lulus')
			{
				$status = 'Tidak Lulus';
			}
			else
			{
				$status = 'Lulus';
			}
		if ( isset($_SESSION['username']) == TRUE )
		{ 
			$siapa=$_SESSION['username'];
			if ($siapa == 'admin')
			{
			$this->Mod_master->Update($id,"data_user","no_reg",$status);
			?>
			<script type="text/javascript">
			javascript:history.go(-1);
			</script>
			<?php
			}
			else
			{
				?>
				<script type="text/javascript" language="javascript">
				alert("Anda tidak berhak masuk ke Control Panel Admin...!!!");
				window.location = "<?php echo base_url(); ?>index.php"
				</script>
				<?php
			}
		}
		else
		{
			?>
			<script type="text/javascript" language="javascript">
			alert("Anda harus login...!");
			window.location = "<?php echo base_url(); ?>index.php"
			</script>
			<?php
		}
	}
	

	function cetak()
	{
		if ( isset($_SESSION['username']) == TRUE )
		{ 
			$siapa=$_SESSION['username'];
			if ($siapa == 'admin')
			{

			$d = $this->uri->segment(3);
			$id = $d."/REK/2013";
		$data = array();
		$file=$this->Mod_master->Ambilgambar($id);
		$data["gambar"]=$file;
		$data["noreg"]=$id;
		$semua["dataq"]=$this->Mod_utama->Ambildata($id, 'data_user');
		$dataq=$semua["dataq"];
		foreach($dataq->result() as $n){}
		$data["nama"]=$n->nama;
		$data["gender"]=$n->LP;
		$data["ktp"]=$n->ktp;
		$data["alamat"]=$n->alamat;
		$data["tplahir"]=$n->tmp_lahir;
		$data["tgllahir"]=$n->tgl_lahir;
		$data["status"]=$n->status;
		$data["bidang"]=$this->Mod_utama->Namabidang($id);
		$data["agama"]=$n->agama;
		$data["tlp"]=$n->tlp;
		$data["email"]=$n->email;
		$cetak = $this->Mod_master->Report('formal',$id,'no_reg','kat','data_pendidikan');
		$data["pend"] = $cetak;
		
			$this->load->view('master/report', $data);
			
			}
			else
			{
				?>
				<script type="text/javascript" language="javascript">
				alert("Anda tidak berhak masuk ke Control Panel Admin...!!!");
				window.location = "<?php echo base_url(); ?>index.php"
				</script>
				<?php
			}
		}
		else
		{
			?>
			<script type="text/javascript" language="javascript">
			alert("Anda harus login...!");
			window.location = "<?php echo base_url(); ?>index.php"
			</script>
			<?php
		}
	}
	
}

/* End of file master.php */
/* Location: ./application/controllers/master.php */