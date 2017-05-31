<style>
	.tab_backup_restore{
		margin:12px;
	}
</style>
<div class="tab_1 full_width" style="height:85px;">
    <span class="title_block bo_ra1">
        <span class="fl_dent">
            <h4>
                Backup
            </h4>
        </span>
    </span>
    <div class="tab_backup_restore">
	    <select id='select_folder_backup' style="width:50%;">Choose folder backup:
	    	<option value='<?php echo $_SERVER["DOCUMENT_ROOT"]."/jobtraq/app/webroot/upload"; ?>' selected='selected'>
	    		Folder Upload
	    	</option>
	    	<option value='<?php echo $_SERVER["DOCUMENT_ROOT"]."/jobtraq/app/webroot/css"; ?>'>
	    		Folder CSS
	    	</option>
	    	<option value='<?php echo $_SERVER["DOCUMENT_ROOT"]."/jobtraq/app/webroot/js"; ?>'>
	    		Folder JS
	    	</option>
	    	<option value='<?php echo $_SERVER["DOCUMENT_ROOT"]."/jobtraq"; ?>'>
	    		All
	    	</option>

	    </select>
	    <button id="backup_button" onclick="backup()" style="margin: auto 2%;">Backup</button>
	    <button id="clear_backup_button" onclick="clear_backup()" disabled="">Clear backup file</button>
	    <span id='notice_bk' style='color:#ff0000; text-decoration: none;display:none; text-align: center; width:55%;' href=''>Waiting...</span>
    </div>
</div>

<div class="tab_1 full_width" style="height:110px; margin-top: 25px;">
    <span class="title_block bo_ra1">
        <span class="fl_dent">
            <h4>
                Restore
            </h4>
        </span>
    </span>
    <div class="tab_backup_restore">
	    <span>
		    <form id="restore_form" method="post" enctype="multipart/form-data" action="javascript:void(0)">
			    <input type="file" name="file_backup" id="file_backup" style="width:50%;"/>
			    <button id="upload_button" onclick="upload()" style="margin-left: 2%;" >Upload</button>
			</form>
		</span>
		
		<span>
			Folder restore: <?php echo ROOT.'/ ';?><input type="text" name="folder_restore" id="folder_restore" style="width:38.1%;"/>
			<input type="hidden" id='check_upload' value='0'>
			<button id="restore_button" onclick="restore()" style="margin-left: 2%;" disabled="">Restore</button>
		</span>
		<span id='notice_res' style='color:#ff0000; text-decoration: none; text-align: center; width:55%;' href=''>
		</span>
    </div>
</div>

<div id="content_backup_restore"></div>
<script type="text/javascript">

	var folder_restore=$('#folder_restore');
	folder_restore.keypress(function(){
		 if($('#check_upload').val()=='1'){
		 	if(folder_restore.val()!=""){
			 	$('#restore_button').prop('disabled',false);
			 }
			 else{
			  	$('#restore_button').prop('disabled',true);
			 }
		
		}
	});

	$('#select_folder_backup').change(function(){
		$('#notice_bk').html("");
	});

	function backup () {
		var folder=$('#select_folder_backup').val();
		$.ajax({
			url:'<?php echo URL;?>/settings/administrator_backup_restore/',
			type:'POST',
			data:{task:'backup',folder:folder},
			success:function(){
				$("#notice_bk").fadeIn(500);
			}
		})
		.done(function (html) {
			$('#notice_bk').html(html);
			$('#clear_backup_button').prop('disabled',false);
			$('#backup_button').prop('disabled',true);
			$('#select_folder_backup').prop('disabled',true);
		});
	}

	
	function upload() {
		
    	var fileInput = document.getElementById('file_backup');  
		var file = fileInput.files[0];

	    // fd dung de luu gia tri goi len
	    var fd = new FormData();
	    fd.append('file', file);
	    fd.append('task', 'upload');
	    fd.append('folder_restore','folder_resstore')
	    // xhr dung de goi data bang ajax
	    var xhr = new XMLHttpRequest();
	    xhr.open('POST', '<?php echo URL;?>/settings/administrator_backup_restore/', true);


	    xhr.onload = function(result) {
	      if (this.status == 200) {
	       result.innerHTML=this.response; 
	       document.getElementById('notice_res').innerHTML=result.innerHTML;
	      
	       if($('#folder_restore').val()!=""){
	       		$('#restore_button').prop('disabled',false);
	       		$('#check_upload').val('1');
	       }else{
	       		$('#check_upload').val('1');
	       }

	      };
	    };

	    xhr.send(fd);
	}


	function restore(){
		$.ajax({
			url:'<?php echo URL;?>/settings/administrator_backup_restore/',
			type:'POST',
			data:{task:'restore',folder:folder_restore.val()},
			success:function(){
				$("#notice_res").fadeOut(500);
			}
		})
		.done(function (html) {
			$('#notice_res').html(html);
			$('#restore_button').prop('disabled',true);
			setTimeout(function(){
				$('#notice_res').fadeOut(500);
			}, 2000);
		});
	}


	function clear_backup(){
		$.ajax({
			url:'<?php echo URL;?>/settings/administrator_backup_restore/',
			type:'POST',
			data:{task:'clear_backup'},
			success:function(){
				$("#notice_bk").fadeIn(500);
			}
		})
		.done(function (html) {
			$('#notice_bk').html(html);
			$('#clear_backup_button').prop('disabled',true);
			$('#backup_button').prop('disabled',false);
			$('#select_folder_backup').prop('disabled',false);
			
		});
	}
</script>