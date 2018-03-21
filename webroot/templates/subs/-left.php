	<div id="sideLeft">

		<?=$sub_box_menu2?>
	
		<div id="header-catalog"><a href="/catalog">Зоомагазин</a> <span class="kolall"><?=$sub_box_fullcount?></span></div>

		<div id="search">
			<form method="get" id="searchform" action="/search">
			<fieldset class="search">
			<input type="text" name="search" class="box1" value="Поиск товаров" onfocus="if(this.value=='Поиск товаров') this.value='';" onblur="if(this.value.trim()=='') this.value='Поиск товаров'">
			<button class="btn" name="sa" title="Поиск">Поиск</button>
			</fieldset>
			</form>
		</div>

		<?=$sub_box_catalog?>

		<div id="header-brands">Производители</div>
		<div id="brands" class="column2">
			<?=$sub_box_brands?>
		</div>


		<div id="left-podbor-block">
			<div id="left-podbor-header">Подбор товаров</div>
			<div id="left-podbor-filter">
				<?=$sub_box_filter?>
			</div>
		</div>

		<div id="header-metki">Метки</div>
		<div id="tags">
			<?=$sub_box_tagcloud?>
		</div>


	</div>
