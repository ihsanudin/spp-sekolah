
			<h1>Daftar Kelompok Tagih</h1>
			<div id="err-msg" class="error msg" style="display:none">tes</div>
			<div id="info-msg" class="information msg" style="display:none">tes</div>

			<div id="form-layer" style="display:none">
			<form id="myform" class="uniform" action="<?php echo (@$action_url);?>">
				<fieldset>
					<legend>Form Master Kelompok Tagih</legend>
					<dl class="inline">
						<dt><label for="id_rate">Tagihan</label></dt>
						<dd>
							<select id="id_rate" name="id_rate" class="medium">
								<?php foreach ($list_tarif as $r) : ?>
								<option value="<?php echo $r->get_id(); ?>"><?php echo $r->get_name(); ?></option>
								<?php endforeach; ?>
							</select>
						</dd>

						<dt><label for="nama">Kelompok ditagih</label></dt>
						<dd>
								<strong> Kelas </strong><br/>
								<input id="grouping" name="grouping" value="kelas" type="hidden" />
								<select id="kelas" name="kelas" class="small select_grouping">
									<option value="99">Semua</option>
									<option value="0">TK</option>
									<option value="1">1 (SD)</option>
									<option value="2">2 (SD)</option>
									<option value="3">3 (SD)</option>
									<option value="4">4 (SD)</option>
									<option value="5">5 (SD)</option>
									<option value="6">6 (SD)</option>
									<option value="7">7 (SMP)</option>
									<option value="8">8 (SMP)</option>
									<option value="9">9 (SMP)</option>
								</select>
								<hr />
								<strong> Siswa </strong><br/>
								<input id="siswa" type="text" name="siswa" class="medium select_grouping" value="<?php echo (@$sess->nama);?>" />
								<div id="peserta-container" style="margin-top: 10px; display: none; -moz-border-radius: 5px 5px 5px 5px; background: no-repeat scroll 10px 13px #E3FFDE; border: 1px solid #6CD858; margin-bottom: 10px; padding: 10px 10px 10px 37px; ">
									Daftar sementara:
									<ol id="tmp_siswa">
									</ol>
								</div>
						</dd>
					</dl>
					
				</fieldset>
				<div class="buttons" style="text-align:right;">
					<button type="button" class="button grey" id="save-button">Simpan</button>
					<button type="button" class="button white" id="cancel-button">Batal</button>
				</div>
			</form>
			</div>

			<div id="list-layer">
			<form name="frm-filter-cat" id="frm-filter-cat" method="post" action="<?php echo (@$filter_url);?>">

			<div class="buttons" style="text-align:right;">
				<button type="button" class="button green" id="new-button">Tambah</button>
			</div>
					<div>
						<label for"keyword"><strong>Filter: </strong></label>
						<input type="text" class="medium submit-filter" name="keyword" id="keyword" value="<?php echo empty($keyword) ? 'cari ...' : $keyword; ?>" onfocus="if (this.value=='cari ...') this.value='';" />
					</div>
				
			
			<br/>
			<table id="tabel" class="gtable">
				<thead>
				<tr>
					<th><div align="left"><strong>Tagihan</strong></div></th>
				  <th><div align="left"><strong>Peserta</strong></div></th>
					<th><div align="center"><strong>Pilihan</strong></div></th>
				</tr>
				</thead>
				<tbody>
				<?php if (empty($list_data)) : ?>
				<tr>
					<td colspan="3">Data tidak ditemukan</td>
				</tr>
				<?php else : ?>
				<?php foreach ($list_data as $row) : ?>
				<tr>
					<td><?php echo $row->tagihan; ?></td>
					<td><?php echo $row->peserta; ?></td>
					<td style="text-align:center;">
						<a title="Delete" href="<?php echo sprintf('%s/%d/%s', site_url('otogroup/delete/'), $row->id, md5($row->id) ); ?>" class="delete-row"><img alt="Delete" src="images/icons/cross.png"></a>
					</td>
				</tr>
				<?php endforeach; ?>
				<?php endif; ?>
				</tbody>
			</table>
		<div class="tablefooter clearfix">
						<div class="actions">
							Keterangan: <img alt="Edit" src="images/icons/edit.png"> Ubah data  <img alt="Delete" src="images/icons/cross.png"> Hapus data
						</div>
						<div class="pagination">
							<a href="<?php echo $prev_page; ?>" class="paging-leaf">Prev</a>
							<?php if ($total_page > 1) : ?>
							<?php foreach (range($first_range, $last_range) as $p) : ?>
							<a <?php echo ($p==$page ? 'class="current"' : ''); ?> href="<?php echo $p; ?>" class="paging-leaf"><?php echo $p; ?></a>
							<?php endforeach; ?>
							...
							<?php endif; ?>
							<input type="text" title="loncat ke halaman ..." name="page" value="<?php echo $total_page; ?>" id="page" style="width: 25px; text-align: right;" class="submit-filter" />
							<a href="<?php echo $next_page; ?>" class="paging-leaf">Next</a>
						</div>
					</div>
		</div>

		</form>
		</div><!--list-layer-->


			<script>
				jQuery('keyword').change(function() {
					//document.getElementById('frm-filter-cat').submit();
					flashDialog('err-msg', 'Fitur ini masih dalam pengembangan', 5);
				});
				jQuery('#new-button').click(function() {
					jQuery('#form-layer').slideDown();
					jQuery('#list-layer').hide();
				});
				jQuery('#cancel-button').click(function() {
					jQuery('#form-layer').slideUp();
					jQuery('#list-layer').show();
				});
				jQuery('#save-button').click(function() {
					var url = jQuery('#myform').attr('action');
					var data = jQuery('#myform').serialize();

					jQuery.post(url, data, function(response) {
						if (! response.success) {
							flashDialog('err-msg', response.message, 5);
						} else {
							flashDialog('info-msg', response.message, 2);
							setTimeout(function() { document.location.href=document.location.href; }, 3000);
						}
					});
				});

			// ---- Auto complete
			jQuery('#siswa').jsonSuggest({
				minCharacters: 3,
				url: '<?php echo ($ajax_siswa_url);?>',
				onSelect: function(item) {
					if (! jQuery('#peserta-container').is(':visible')) jQuery('#peserta-container').toggle();

					peserta = '<li><input type="hidden" name="peserta[]" value="' + item.id + '" /><a title="Delete" href="javascript:void(0);" class="rm-siswa" onclick="remove_siswa(this);"><img alt="Delete" src="images/icons/cross.png"></a> ' + item.nama + ' - ' + item.kelas + ' ( ' + item.jenjang + ' )</li>';
					jQuery('#tmp_siswa').append(peserta);
					jQuery('#siswa').val('');
				},
				onEnter: function() {
					// cegah form submit
					return false;
				}
			});

			function remove_siswa(item) {
				var li = jQuery(item).parent();
				li.fadeOut();
				li.remove();
			};

			jQuery('.select_grouping').focus(function() {
				jQuery('#grouping').val(this.id);
			});

			jQuery('a.paging-leaf').click(function(e) {
				jQuery('#page').val(jQuery(this).attr('href'));
				jQuery('#frm-filter-cat').submit();
				e.preventDefault();
			});

			jQuery('.submit-filter').keypress(function(e){
				if (e.keyCode == 13) jQuery('#frm-filter-cat').submit();
			});

			jQuery('#keyword').keypress(function(e){
				jQuery('#page').val(1);
			});

			</script>
