<?php
use yii\helpers\Url;
use vendor\pceuropa\menu\Module;
use vendor\pceuropa\menu\MenuAsset;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Modal;
use yii\widgets\Pjax;


MenuAsset::register($this);
$this->registerCss('
#ul0, #ul1 {border: solid 1px #E0DEDE;min-width:20px; min-height:50px}
.navbar-brand-left {border: solid 1px #F0F0F0;margin-right:0}
#trash {
	background: url("'.Url::to("@web/images/").'trash.png") no-repeat center top;
	background-size: contain;
	height:100px;

');

Pjax::begin([
	'id' => 'pjaxMenuNav',
]);	?>

<div id="container-nav">
  <nav class="navbar navbar-default">
        <div class="container-fluid">
          
		  <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">L</a>
          </div>
		  
          <div id="navbar" class="navbar-collapse collapse">
		  
            <ul id="ul0" class="nav navbar-nav">
			
				<?php 
				$m = new Module([]);
				foreach ($m->Left() as $k => $v){

					echo array_key_exists('url', $v) ? Module::Link($v) : Module::DropMenu($v);
				}
				?>
            </ul>
			
            <ul id="ul1" class="nav navbar-nav navbar-right">
			
				<?php foreach ($m->Right() as $k => $v){

					echo array_key_exists('url', $v) ? Module::Link($v) : Module::DropMenu($v);
				}
				?>
            </ul>
			
          </div><!--/.nav-collapse -->
        </div><!--/.container-fluid -->
      </nav>
	  
</div>
<?php Pjax::end();?>


<div class="row">
<div class="col-md-8 well">
	<h4>Add link or DropMenu</h4>
	<?= $this->render('_form', ['model' => $model,]) ?>
</div>
<div id="edit" class="well col-md-1 col-md-offset-1"><h4>Drop there to Edit</h4></div>
<div id="trash" class="well col-md-1 col-md-offset-1">Drop there to delete</div>

</div>





	
<?php 
	Modal::begin([
		'header' => 'Update Link or DropMenu',
		'id' => 'modalUpdate',
		'clientOptions' => ['backdrop' => 'static', 'keyboard' => FALSE]
	]);
	
		echo "<div id='contentModal'></div>";
	Modal::end(); ?>

<?php $this->registerJS('

var config = {
	group: "nav",
	onUpdate: function (evt) {
		
		console.log(evt.item); // evt.item.id

		var data = {
			gr: evt.from.id,
			oldIndex: evt.oldIndex,
			newIndex: evt.newIndex
		}; 

		$.ajax({
			url: "'.Url::to(['sort/same-group']).'",
			type: "post",
			data: data,
			success: function (response) {
					if (response.success === false) {console.log(response.message);}
					if (response.success === true) {console.log(response.message);}

				},
			error: function (XMLHttpRequest, textStatus, errorThrown) {
				alert(textStatus);
			}
		});
	},

	onAdd: function (evt) {
		var data = {
			id: evt.item.id,
			gr: evt.target.id,
			newIndex: evt.newIndex
		};

		$.ajax({
			url: "'.Url::to(['sort/not-same-group']).'",
			type: "post",
			data: data,
			success: function (response) {
					if (response.success === false) {console.log(response.message);}
					if (response.success === true) {console.log(response.message);}
				},
			error: function (XMLHttpRequest, textStatus, errorThrown) {
				alert(textStatus);
			}
		});
	},
}

Sortable.create(ul0, config);
Sortable.create(ul1, config);
Sortable.create(trash, {
	group: "nav",
	onAdd: function (/**Event*/evt) {
		var el = evt.item; 
		el && el.parentNode.removeChild(el);
		console.log(evt.item.id);

		var data = {
			id: evt.item.id,
			gr: evt.from.id,
		};
	   
		$.ajax({
			url: "'.Url::to(["index/delete"]).'",
			type: "post",
			data: data,
			success: function (response) {
					if (response.success === false) {console.log(response.message);}
					if (response.success === true) {console.log(response.message);}
				},
			error: function (XMLHttpRequest, textStatus, errorThrown) {
				alert(textStatus);
			}
		});
	},
});

Sortable.create(edit, {
	group: "nav",
	onAdd: function (evt) {

		$("#modalUpdate").modal("show")
		.find("#contentModal")
		.load( "'.Url::to(['index/update']).'?id=" + evt.item.id );
		
    },
});

$("#modalUpdate").on("hidden.bs.modal", function () {
   $.pjax.reload("*");
})
'.$m->whichElementsDrop(), 3);
?>
