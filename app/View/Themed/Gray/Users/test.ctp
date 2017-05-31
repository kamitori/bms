<form method="POST">
<input type="text" name="url" value="" />
<select name="name"><option value="cang" <?php if($option == 'cang') echo 'selected'; ?>>Chi Cang</option><option value="minh" <?php if($option == 'minh') echo 'selected'; ?>>Thim Minh</option></select>
<input type="submit" name="submit" value="ok" />
</form>