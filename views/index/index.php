<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Modal;
use yii\widgets\Pjax;
use devtrekker\menu\Module;

devtrekker\menu\MenuAsset::register($this);

$this->registerCss('
    #ul0, #ul1 {
        min-width: 20px; 
        min-height: 50px; 
        border: solid 1px #DCD8D8
    }
    
    .navbar-brand-left {
        border: solid 1px #F0F0F0; 
        margin-right:0
    }
    
    #additional-info {
        color:#9D9B9B;
    }
    
    .edit-trash-box {
        margin-top:40px
    }
    
    .navbar-nav > li > .dropdown-menu {
        min-height: 30px;
    }
    
    .ghost {
        opacity: 0.3;outline: 0;background: #C8EBFB;
    }
    
    a {
        outline: 0;
    }
    
    #trash, #edit {
        height:120px;
        margin-top: 5px;
    }
    
    #trash i, #edit i{
        font-size:33px;
        margin-top:10px;
        color:#ccc
    }
    
    #trash li, #edit li{
        text-align:center;
        list-style-type: none;
        font-size:200%;
    }
    
    #trash li a {
        color:red; opacity: 1;
    }
    
    #edit li a {
        color:#1D9841;opacity: 1;
    }
    
    .dropdown-menu .divider {
        height: 5px;
    }
');

Pjax::begin(['id' => 'pjaxMenuNav']);  
?>

    <h3>Menu Editor</h3>
    <div class="row well" style="margin: 5px;">
        <h4>Menu Item Creator</h4>
        <?= $this->render('_form', ['model' => $model,]) ?>
        <br />
    </div>

    <div class="row well" style="margin: 5px;" id="container-nav">
        <h4>Sample Menu</h4>
        <p>Select an element, and drag below to edit or trash</p>
        <nav class="navbar navbar-inverse">
            <div class="container-fluid">
              
              <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                  <span class="sr-only">Toggle navigation</span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#"></a>
              </div>
              
              <div id="navbar" class="navbar-collapse collapse devtrekker-menu">
                <ul id="ul0" class="nav navbar-nav">
                    <?php 
                    foreach (Module::NavbarLeft() as $k => $v){
                        echo array_key_exists('url', $v) ? Module::Link($v) : Module::DropMenu($v);
                    }
                    ?>
                </ul>
                
                <ul id="ul1" class="nav navbar-nav navbar-right ">
                    <?php foreach (Module::NavbarRight() as $k => $v){
                        echo array_key_exists('url', $v) ? Module::Link($v) : Module::DropMenu($v);
                    }
                    ?>
                </ul>
              </div><!--/.nav-collapse -->
              
            </div><!--/.container-fluid -->
        </nav>
    </div>
<?php Pjax::end();?>

<div class="edit-trash-box row" style="margin: 5px;">
  <div class="row">
    <div class="col-md-6">
        <div class="panel panel-success">
            <div class="panel-heading"><h3 class="panel-title"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span> Drop here to edit</h3></div>
            <div class="panel-body">
                <div id="edit" class="well well-sm"></div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="panel panel-danger">
            <div class="panel-heading"><h3 class="panel-title"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Drop here to trash</h3></div>
            <div class="panel-body">
            <div id="trash" class="well well-sm"></div>
            </div>
        </div>
    </div>
  </div>
</div>


<?php 
    Modal::begin([
        'header' => 'Update Link or DropMenu',
        'id' => 'modalUpdate',
        'clientOptions' => ['backdrop' => 'static', 'keyboard' => false]
    ]);
    
    echo "<div id='contentModal'></div>";
    Modal::end(); 
?>

<?php 
$this->registerJS('
    openMenu = null;
        
    var config = {
        group: "nav",
        animation: 0,
        ghostClass: "ghost",
        
        onUpdate: function (evt) {
            
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
        
      onMove: function (/**Event*/evt) {
        id = ".devtrekker-menu " + "#"+ evt.related.id + " .dropdown-toggle"
            targetMenu = evt.related.id;
                
                            console.log(evt.dragged.dataset.type);

            if (evt.to.id != "trash" && evt.to.id != "edit" && evt.dragged.childElementCount == 1) {
                if (openMenu == null || openMenu != targetMenu ) {
                            $(id).dropdown("toggle");
                            openMenu = targetMenu;
                        } 
            }
                
      
          // return false; — for cancel
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
'.Module::whichElementsDrop(), 3);
?>
