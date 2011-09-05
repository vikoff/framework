
<?=$pagination;?>

<?
if(getVar($collection)){

	foreach($collection as $item)
		echo $item;
		
}else{
	echo 'Запесей не найдено';
}
?>

<?=$pagination;?>

