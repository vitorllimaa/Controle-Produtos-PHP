<?php if(!class_exists('Rain\Tpl')){exit;}?><div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Lista de Usuários
    </h1>
    <ol class="breadcrumb">
      <li><a href="/admin"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active"><a href="/admin/users">Usuários</a></li>
    </ol>
  </section>
  <!-- Main content -->
  <section class="content">
  
    <div class="row">
      <div class="col-md-12">
        <div class="box box-primary">
              
          <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
            <li class="nav-item">
              <a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="" role="tab" aria-controls="pills-home" aria-selected="true">Mercado livre Click</a>
            </li>
            <style>#p{padding: 5px;
              text-align: right;
              color:grey
              }
              #v{
                text-align: center;
              }
              #c{
                text-align: center;
              }
            </style>
            <div id="p"><p>TOTAL DE PRODUTOS: <?php echo htmlspecialchars( $pages, ENT_COMPAT, 'UTF-8', FALSE ); ?></p></div>
            </li>
          </ul>
          <a href="/admin/preco/mlclick/gerar_planilha_ml_click"><button type="button" class="btn btn-success">Gerar Planilha</button></a>
              <div class="box-body no-padding">
                <table class="table table-striped">
                  <thead>
                    <tr>
                      <th style="width: 60px">ID</th>
                      <th>Nome</th>
                      <th>Estoque MKTP</th>
                      <th>Estoque ERP</th>
                      <th id="c">Comparativo</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php $counter1=-1;  if( isset($page) && ( is_array($page) || $page instanceof Traversable ) && sizeof($page) ) foreach( $page as $key1 => $value1 ){ $counter1++; ?>
                    <tr>
                      <td><?php echo htmlspecialchars( $value1["id_produto"], ENT_COMPAT, 'UTF-8', FALSE ); ?></td>
                      <td><?php echo htmlspecialchars( $value1["name"], ENT_COMPAT, 'UTF-8', FALSE ); ?></td>
                      <td><?php echo htmlspecialchars( $value1["preco"], ENT_COMPAT, 'UTF-8', FALSE ); ?></td>
                      <td><?php echo htmlspecialchars( $value1["preco_venda"], ENT_COMPAT, 'UTF-8', FALSE ); ?></td>
                      <td id="v"><?php if( $value1["Comparativo"]=='Preço correto!' ){ ?><small class="label pull-right bg-green">Preço correto</small>
                        <?php }else{ ?><small class="label pull-right bg-red">Alerta - divergente</small><?php } ?></td>
                    </tr>
                    <?php } ?>
                  </tbody>
                </table>
                <nav aria-label="...">
                  <ul class="pagination pagination-sm">
                    <li><a href="#">«</a></li>
                    <?php $counter1=-1;  if( isset($pg) && ( is_array($pg) || $pg instanceof Traversable ) && sizeof($pg) ) foreach( $pg as $key1 => $value1 ){ $counter1++; ?>
                    <li class="page-item"><a class="page-link" href="<?php echo htmlspecialchars( $value1["link"], ENT_COMPAT, 'UTF-8', FALSE ); ?>"><?php echo htmlspecialchars( $value1["page"], ENT_COMPAT, 'UTF-8', FALSE ); ?></a></li>
                    <?php } ?>
                    <li><a href="#">»</a></li>
                  </ul>
                </nav>
              </div>
            </div>
      </div>
    </div>
  
  </section>
  <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->