<?php echo $this->Form->create($model); ?>

<?php if( isset($admin) ){ ?>
	<div id="print" style="float:left"><a href="/users/index/old" title="Trở lại giao diện cũ">Chuyển sang giao diện tìm kiếm thành viên</a></div>
<?php } ?>

	<div id="divChucNang" style="float:right">
	<!--<input value=" Tìm " type="submit" class="Button" id="btnsearch">-->
	<?php if( isset($admin) ){ ?>
		<a style="margin-right: 2px;" class="Button" href="<?php echo $controller;?>/them"> Thêm </a>
	<?php } ?>
	</div>

	<br>
	<center><h2>Danh Sách Nhân Viên Công Ty</h2></center>

	<!--SEARCH-->
<!--	<table class="Data">
		<tr>
			<td style="width: 145px;">Tên</td>
			<td style="width: 148px;">Trực thuộc</td>
			<td style="width: 81px;">Điện thoại</td>
		</tr>

		<tr>-->
			<!--<td>--><?php //echo $this->Form->input('ten', array('style' => 'width:99%'));?><!--</td>-->
			<!--<td>--><?php //echo $this->Form->input('codetbl_id', array('style' => 'width:99%;', 'options' => $nhom, 'empty' => '----', 'onchange' => '$("#btnsearch").click()'));?><!--</td>-->
			<!--<td>--><?php //echo $this->Form->input('tel', array('style' => 'width:99%;'));?><!--</td>-->
		<!-- </tr>
	</table>-->
	<!--SEARCH-->

<?php echo $this->Form->end(); ?>

<table id="TooltipChitiet" class="CssTable" style="margin-left:10px;width: 95%;">
	<?php foreach($datas as $key => $data){ ?>
		<?php if(is_string($data) || !isset($data[0]))continue; ?>
		<tr style="text-align:center;height:54px;">
			<td colspan="4"><span style="font-weight:bold; text-transform: uppercase;">
			 ► <?php echo $nhom[$key]; ?></span>
			 </td>
		</tr>

		<!--ADD TRUONG QUAN LY-->
		<?php if( isset($tmp_truong_nhoms[$key]) ){


			if( isset($tmp_truong_nhoms[$key][0]) )
			{
				echo '<tr><td colspan="4"><center style="width: 157px; margin-left: 434px;">';
					echo $this->element('../Users/index_td', array('key_tmp' => 0, 'data' => $tmp_truong_nhoms[$key], 'user' => $tmp_truong_nhoms[$key][0]['User']));
				echo '</center></td></tr>';
			}


			if( isset($tmp_truong_nhoms[$key][2]) )
			{
				echo '<tr>';
					echo '<td colspan="2"><center style="width: 157px; margin-left: 234px;">';
					echo $this->element('../Users/index_td', array('key_tmp' => 1, 'data' => $tmp_truong_nhoms[$key], 'user' => $tmp_truong_nhoms[$key][1]['User']));
					echo '</center></td>';
					echo '<td colspan="2"><center style="width: 157px; margin-left: 120px;">';
					echo $this->element('../Users/index_td', array('key_tmp' => 2, 'data' => $tmp_truong_nhoms[$key], 'user' => $tmp_truong_nhoms[$key][2]['User']));
					echo '</center></td>';
				echo '</tr>';
			}elseif( isset($tmp_truong_nhoms[$key][1]) )
			{
				echo '<tr>';
					echo '<td colspan="4"><center style="width: 157px; margin-left: 434px;">';
					echo $this->element('../Users/index_td', array('key_tmp' => 1, 'data' => $tmp_truong_nhoms[$key], 'user' => $tmp_truong_nhoms[$key][1]['User']));
					echo '</center></td>';
				echo '</tr>';
			}
		} ?>
		<!--END ADD TRUONG QUAN LY-->

		<?php $so_dong = ceil(count($data)/4);
			for( $i = 1; $i <= $so_dong; $i++ )
			{
				echo "<tr>";
				for( $j = 0; $j < 4; $j++ )
				{
					if( isset($data[(($i-1)*4+$j)]) )
					{
						$user = $data[(($i-1)*4+$j)]['User'];

					?>
						<?php echo '<td>'.$this->element('../Users/index_td', array('data' => $data, 'user' => $user, 'key' => $key, 'i' => $i, 'j' => $j)).'</td>'; ?>
				<?php }else{
						echo "<td></td>";
					}
				}
				echo "</tr>";
			}
		} ?>
</table>

<br><br><br><br>
<!--END CONTENT USER-->