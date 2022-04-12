<?php
    include '../config/config.php';
    require '../Excel/vendor/autoload.php';

    use PhpOffice\PhpSpreadsheet\SpreadSheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
    use PhpOffice\PhpSpreadsheet\IOFactory;

    $avaliable_goods = file_get_contents("../data-1.json");
    $avaliable_goods = json_decode($avaliable_goods, true);

    if($_POST['type'] == 'get_available_goods'){
        $html = '';
        $html2 = '';
        $html_select_citys = '<option value="" selected disabled>Elige una ciudad</option>';
        $array_citys = array();
        $html_select_types = '<option value="" selected disabled>Elige un tipo</option>';
        $array_types = array();
        foreach($avaliable_goods as $data){
            $html .= '<div style="margin-bottom:10px;">';
            $html .= '<img src="img/home.jpg" width="50%" style="float:left; margin-right:15px;">';
            $html .= '<p>
                        <strong style="font-weight: bold;">Dirección: </strong>'.$data['Direccion'].'<br>
                        <strong style="font-weight: bold;">Ciudad: </strong>'.$data['Ciudad'].'<br>
                        <strong style="font-weight: bold;">Teléfono: </strong>'.$data['Telefono'].'<br>
                        <strong style="font-weight: bold;">Código Postal: </strong>'.$data['Codigo_Postal'].'<br>
                        <strong style="font-weight: bold;">Tipo: </strong>'.$data['Tipo'].'<br>
                        <strong style="font-weight: bold;">Precio: </strong>'.$data['Precio'].'<br>
                      </p>
                      <button type="button" class="btn save_avaliables" id_available="'.$data['Id'].'">Guardar</button>';
            $html .= '</div>';

            if(!in_array($data['Ciudad'], $array_citys)){
                array_push($array_citys, $data['Ciudad']);
                $html_select_citys .= '<option value="'.$data['Ciudad'].'">'.$data['Ciudad'].'</option>';
            }
            
            if(!in_array($data['Tipo'], $array_types)){
                array_push($array_types, $data['Tipo']);
                $html_select_types .= '<option value="'.$data['Tipo'].'">'.$data['Tipo'].'</option>';
            }

            $query = "SELECT `id`, `id_available` FROM `available_goods_saved` WHERE `id_available` = $data[Id]";
            $s_query = mysqli_query($con, $query);
            $c_query = mysqli_num_rows($s_query);
            $d_query = mysqli_fetch_assoc($s_query);
            if($c_query>0){
                $html2 .= '<div style="margin-bottom:10px;">';
                $html2 .= '<img src="img/home.jpg" width="50%" style="float:left; margin-right:15px;">';
                $html2 .= '<p>
                            <strong style="font-weight: bold;">Dirección: </strong>'.$data['Direccion'].'<br>
                            <strong style="font-weight: bold;">Ciudad: </strong>'.$data['Ciudad'].'<br>
                            <strong style="font-weight: bold;">Teléfono: </strong>'.$data['Telefono'].'<br>
                            <strong style="font-weight: bold;">Código Postal: </strong>'.$data['Codigo_Postal'].'<br>
                            <strong style="font-weight: bold;">Tipo: </strong>'.$data['Tipo'].'<br>
                            <strong style="font-weight: bold;">Precio: </strong>'.$data['Precio'].'<br>
                          </p>
                          <button type="button" class="btn delete_avaliables" id_available="'.$d_query['id'].'">Eliminar</button>';
                $html2 .= '</div>';
            }
        }

        $html .= '<script>
                    $(".save_avaliables").on("click", function(){
                        $.ajax({
                          type: "POST",
                          url: "php/functions.php",
                          data:{
                            "type": "saved_available_goods",
                            "id_available": $(this).attr("id_available")
                          },
                          dataType: "json",
                          success: function(data){
                            if(data.response == "correct"){
                                alert(data.info);
                                $("#divResultadosBusqueda2").html(data.html);
                            }else{
                                alert(data.info);
                            }
                          },
                          error: function(data){
                            console.log("Error guardando Bienes disponibles");
                          }
                        });
                    });
                  </script>';
        
        $html2 .= '<script>
                    $(".delete_avaliables").on("click", function(){
                        $.ajax({
                          type: "POST",
                          url: "php/functions.php",
                          data:{
                            "type": "delete_available_goods",
                            "id_available": $(this).attr("id_available")
                          },
                          dataType: "json",
                          success: function(data){
                            if(data.response == "correct"){
                                alert(data.info);
                                $("#divResultadosBusqueda2").html(data.html);
                            }else{
                                alert(data.info);
                            }
                          },
                          error: function(data){
                            console.log("Error eliminando Bienes disponibles");
                          }
                        });
                    });
                  </script>';

        echo json_encode(array(
            'response' => 'correct',
            'info' => $html,
            'info2' => $html2,
            'info_citys' => $html_select_citys,
            'info_types' => $html_select_types
        ));
    }
    if($_POST['type'] == 'filter_available_goods'){
        
        $html = '';
        $html2 = '';
        foreach($avaliable_goods as $data){
            $query = "SELECT `id`, `id_available` FROM `available_goods_saved` WHERE `id_available` = $data[Id]";
            $s_query = mysqli_query($con, $query);
            $c_query = mysqli_num_rows($s_query);
            $d_query = mysqli_fetch_assoc($s_query);
            $price = explode(';', $_POST['precio']);
            $lowest_price = $price[0];
            $highest_price = $price[1];
            $actually_price = str_replace(',', '', str_replace('$', '', $data['Precio']));
            if($actually_price >= $lowest_price && $actually_price <= $highest_price){
                if(isset($_POST['ciudad']) && isset($_POST['tipo'])){
                    if($_POST['ciudad'] == $data['Ciudad'] && $_POST['tipo'] == $data['Tipo']){
                        $html .= '<div style="margin-bottom:10px;">';
                        $html .= '<img src="img/home.jpg" width="50%" style="float:left; margin-right:15px;">';
                        $html .= '<p>
                                    <strong style="font-weight: bold;">Dirección: </strong>'.$data['Direccion'].'<br>
                                    <strong style="font-weight: bold;">Ciudad: </strong>'.$data['Ciudad'].'<br>
                                    <strong style="font-weight: bold;">Teléfono: </strong>'.$data['Telefono'].'<br>
                                    <strong style="font-weight: bold;">Código Postal: </strong>'.$data['Codigo_Postal'].'<br>
                                    <strong style="font-weight: bold;">Tipo: </strong>'.$data['Tipo'].'<br>
                                    <strong style="font-weight: bold;">Precio: </strong>'.$data['Precio'].'<br>
                                  </p>
                                  <button type="button" class="btn save_avaliables" id_available="'.$data['Id'].'">Guardar</button>';
                        $html .= '</div>';
                        if($c_query>0){
                          $html2 .= '<div style="margin-bottom:10px;">';
                          $html2 .= '<img src="img/home.jpg" width="50%" style="float:left; margin-right:15px;">';
                          $html2 .= '<p>
                                      <strong style="font-weight: bold;">Dirección: </strong>'.$data['Direccion'].'<br>
                                      <strong style="font-weight: bold;">Ciudad: </strong>'.$data['Ciudad'].'<br>
                                      <strong style="font-weight: bold;">Teléfono: </strong>'.$data['Telefono'].'<br>
                                      <strong style="font-weight: bold;">Código Postal: </strong>'.$data['Codigo_Postal'].'<br>
                                      <strong style="font-weight: bold;">Tipo: </strong>'.$data['Tipo'].'<br>
                                      <strong style="font-weight: bold;">Precio: </strong>'.$data['Precio'].'<br>
                                    </p>
                                    <button type="button" class="btn delete_avaliables" id_available="'.$d_query['id'].'">Eliminar</button>';
                          $html2 .= '</div>';
                        }
                    }
                }elseif(isset($_POST['ciudad']) && !isset($_POST['tipo'])){
                    if($_POST['ciudad'] == $data['Ciudad']){
                        $html .= '<div style="margin-bottom:10px;">';
                        $html .= '<img src="img/home.jpg" width="50%" style="float:left; margin-right:15px;">';
                        $html .= '<p>
                                    <strong style="font-weight: bold;">Dirección: </strong>'.$data['Direccion'].'<br>
                                    <strong style="font-weight: bold;">Ciudad: </strong>'.$data['Ciudad'].'<br>
                                    <strong style="font-weight: bold;">Teléfono: </strong>'.$data['Telefono'].'<br>
                                    <strong style="font-weight: bold;">Código Postal: </strong>'.$data['Codigo_Postal'].'<br>
                                    <strong style="font-weight: bold;">Tipo: </strong>'.$data['Tipo'].'<br>
                                    <strong style="font-weight: bold;">Precio: </strong>'.$data['Precio'].'<br>
                                  </p>
                                  <button type="button" class="btn save_avaliables" id_available="'.$data['Id'].'">Guardar</button>';
                        $html .= '</div>';
                        if($c_query>0){
                          $html2 .= '<div style="margin-bottom:10px;">';
                          $html2 .= '<img src="img/home.jpg" width="50%" style="float:left; margin-right:15px;">';
                          $html2 .= '<p>
                                      <strong style="font-weight: bold;">Dirección: </strong>'.$data['Direccion'].'<br>
                                      <strong style="font-weight: bold;">Ciudad: </strong>'.$data['Ciudad'].'<br>
                                      <strong style="font-weight: bold;">Teléfono: </strong>'.$data['Telefono'].'<br>
                                      <strong style="font-weight: bold;">Código Postal: </strong>'.$data['Codigo_Postal'].'<br>
                                      <strong style="font-weight: bold;">Tipo: </strong>'.$data['Tipo'].'<br>
                                      <strong style="font-weight: bold;">Precio: </strong>'.$data['Precio'].'<br>
                                    </p>
                                    <button type="button" class="btn delete_avaliables" id_available="'.$d_query['id'].'">Eliminar</button>';
                          $html2 .= '</div>';
                        }
                    }
                }elseif(!isset($_POST['ciudad']) && isset($_POST['tipo'])){
                    if($_POST['tipo'] == $data['Tipo']){
                        $html .= '<div style="margin-bottom:10px;">';
                        $html .= '<img src="img/home.jpg" width="50%" style="float:left; margin-right:15px;">';
                        $html .= '<p>
                                    <strong style="font-weight: bold;">Dirección: </strong>'.$data['Direccion'].'<br>
                                    <strong style="font-weight: bold;">Ciudad: </strong>'.$data['Ciudad'].'<br>
                                    <strong style="font-weight: bold;">Teléfono: </strong>'.$data['Telefono'].'<br>
                                    <strong style="font-weight: bold;">Código Postal: </strong>'.$data['Codigo_Postal'].'<br>
                                    <strong style="font-weight: bold;">Tipo: </strong>'.$data['Tipo'].'<br>
                                    <strong style="font-weight: bold;">Precio: </strong>'.$data['Precio'].'<br>
                                  </p>
                                  <button type="button" class="btn save_avaliables" id_available="'.$data['Id'].'">Guardar</button>';
                        $html .= '</div>';
                        if($c_query>0){
                          $html2 .= '<div style="margin-bottom:10px;">';
                          $html2 .= '<img src="img/home.jpg" width="50%" style="float:left; margin-right:15px;">';
                          $html2 .= '<p>
                                      <strong style="font-weight: bold;">Dirección: </strong>'.$data['Direccion'].'<br>
                                      <strong style="font-weight: bold;">Ciudad: </strong>'.$data['Ciudad'].'<br>
                                      <strong style="font-weight: bold;">Teléfono: </strong>'.$data['Telefono'].'<br>
                                      <strong style="font-weight: bold;">Código Postal: </strong>'.$data['Codigo_Postal'].'<br>
                                      <strong style="font-weight: bold;">Tipo: </strong>'.$data['Tipo'].'<br>
                                      <strong style="font-weight: bold;">Precio: </strong>'.$data['Precio'].'<br>
                                    </p>
                                    <button type="button" class="btn delete_avaliables" id_available="'.$d_query['id'].'">Eliminar</button>';
                          $html2 .= '</div>';
                        }
                    }
                }else{
                    $html .= '<div style="margin-bottom:10px;">';
                    $html .= '<img src="img/home.jpg" width="50%" style="float:left; margin-right:15px;">';
                    $html .= '<p>
                                <strong style="font-weight: bold;">Dirección: </strong>'.$data['Direccion'].'<br>
                                <strong style="font-weight: bold;">Ciudad: </strong>'.$data['Ciudad'].'<br>
                                <strong style="font-weight: bold;">Teléfono: </strong>'.$data['Telefono'].'<br>
                                <strong style="font-weight: bold;">Código Postal: </strong>'.$data['Codigo_Postal'].'<br>
                                <strong style="font-weight: bold;">Tipo: </strong>'.$data['Tipo'].'<br>
                                <strong style="font-weight: bold;">Precio: </strong>'.$data['Precio'].'<br>
                              </p>
                              <button type="button" class="btn save_avaliables" id_available="'.$data['Id'].'">Guardar</button>';
                    $html .= '</div>';
                    if($c_query>0){
                      $html2 .= '<div style="margin-bottom:10px;">';
                      $html2 .= '<img src="img/home.jpg" width="50%" style="float:left; margin-right:15px;">';
                      $html2 .= '<p>
                                  <strong style="font-weight: bold;">Dirección: </strong>'.$data['Direccion'].'<br>
                                  <strong style="font-weight: bold;">Ciudad: </strong>'.$data['Ciudad'].'<br>
                                  <strong style="font-weight: bold;">Teléfono: </strong>'.$data['Telefono'].'<br>
                                  <strong style="font-weight: bold;">Código Postal: </strong>'.$data['Codigo_Postal'].'<br>
                                  <strong style="font-weight: bold;">Tipo: </strong>'.$data['Tipo'].'<br>
                                  <strong style="font-weight: bold;">Precio: </strong>'.$data['Precio'].'<br>
                                </p>
                                <button type="button" class="btn delete_avaliables" id_available="'.$d_query['id'].'">Eliminar</button>';
                      $html2 .= '</div>';
                    }
                }
            }
        }
        $html .= '<script>
                    $(".save_avaliables").on("click", function(){
                        $.ajax({
                          type: "POST",
                          url: "php/functions.php",
                          data:{
                            "type": "saved_available_goods",
                            "id_available": $(this).attr("id_available")
                          },
                          dataType: "json",
                          success: function(data){
                            if(data.response == "correct"){
                                alert(data.info);
                                $("#divResultadosBusqueda2").html(data.html);
                            }else{
                                alert(data.info);
                            }
                          },
                          error: function(data){
                            console.log("Error guardando Bienes disponibles");
                          }
                        });
                    });
                  </script>';
        
        $html2 .= '<script>
                    $(".delete_avaliables").on("click", function(){
                        $.ajax({
                          type: "POST",
                          url: "php/functions.php",
                          data:{
                            "type": "delete_available_goods",
                            "id_available": $(this).attr("id_available")
                          },
                          dataType: "json",
                          success: function(data){
                            if(data.response == "correct"){
                                alert(data.info);
                                $("#divResultadosBusqueda2").html(data.html);
                            }else{
                                alert(data.info);
                            }
                          },
                          error: function(data){
                            console.log("Error eliminando Bienes disponibles");
                          }
                        });
                    });
                  </script>';
        echo json_encode(array(
            'response' => 'correct',
            'info' => $html,
            'info2' => $html2
        ));
    }

    if($_POST['type'] == 'saved_available_goods'){
        $id_available = $_POST['id_available'];

        $query = "SELECT `id`, `id_available` FROM `available_goods_saved` WHERE `id_available` = $id_available";
        $s_query = mysqli_query($con, $query);
        $c_query = mysqli_num_rows($s_query);
        if($c_query>0){
            echo json_encode(array(
                'response' => 'error',
                'info' => 'Este bien ya fue guardado'
            ));
            return false;
        }

        $query = "INSERT INTO `available_goods_saved`(`id`, `id_available`) VALUES (NULL, $id_available)";
        $s_query = mysqli_query($con, $query);

        if($s_query){
            $html = '';
            foreach($avaliable_goods as $data){
                $query = "SELECT `id`, `id_available` FROM `available_goods_saved` WHERE `id_available` = $data[Id]";
                $s_query = mysqli_query($con, $query);
                $c_query = mysqli_num_rows($s_query);
                $d_query = mysqli_fetch_assoc($s_query);
                if($c_query>0){
                    $html .= '<div style="margin-bottom:10px;">';
                    $html .= '<img src="img/home.jpg" width="50%" style="float:left; margin-right:15px;">';
                    $html .= '<p>
                                <strong style="font-weight: bold;">Dirección: </strong>'.$data['Direccion'].'<br>
                                <strong style="font-weight: bold;">Ciudad: </strong>'.$data['Ciudad'].'<br>
                                <strong style="font-weight: bold;">Teléfono: </strong>'.$data['Telefono'].'<br>
                                <strong style="font-weight: bold;">Código Postal: </strong>'.$data['Codigo_Postal'].'<br>
                                <strong style="font-weight: bold;">Tipo: </strong>'.$data['Tipo'].'<br>
                                <strong style="font-weight: bold;">Precio: </strong>'.$data['Precio'].'<br>
                              </p>
                              <button type="button" class="btn delete_avaliables" id_available="'.$d_query['id'].'">Eliminar</button>';
                    $html .= '</div>';
                }
            }

            $html .= '<script>
                    $(".delete_avaliables").on("click", function(){
                        $.ajax({
                          type: "POST",
                          url: "php/functions.php",
                          data:{
                            "type": "delete_available_goods",
                            "id_available": $(this).attr("id_available")
                          },
                          dataType: "json",
                          success: function(data){
                            if(data.response == "correct"){
                                alert(data.info);
                                $("#divResultadosBusqueda2").html(data.html);
                            }else{
                                alert(data.info);
                            }
                          },
                          error: function(data){
                            console.log("Error eliminando Bienes disponibles");
                          }
                        });
                    });
                  </script>';

            echo json_encode(array(
                'response' => 'correct',
                'info' => 'Bien guardado correctamente',
                'html' => $html
            ));
        }
    }
    
    if($_POST['type'] == 'delete_available_goods'){
        $id_available = $_POST['id_available'];

        $query = "DELETE FROM `available_goods_saved` WHERE `id`= $id_available";
        $s_query = mysqli_query($con, $query);

        if($s_query){
            $html = '';
            foreach($avaliable_goods as $data){
                $query = "SELECT `id`, `id_available` FROM `available_goods_saved` WHERE `id_available` = $data[Id]";
                $s_query = mysqli_query($con, $query);
                $c_query = mysqli_num_rows($s_query);
                $d_query = mysqli_fetch_assoc($s_query);
                if($c_query>0){
                    $html .= '<div style="margin-bottom:10px;">';
                    $html .= '<img src="img/home.jpg" width="50%" style="float:left; margin-right:15px;">';
                    $html .= '<p>
                                <strong style="font-weight: bold;">Dirección: </strong>'.$data['Direccion'].'<br>
                                <strong style="font-weight: bold;">Ciudad: </strong>'.$data['Ciudad'].'<br>
                                <strong style="font-weight: bold;">Teléfono: </strong>'.$data['Telefono'].'<br>
                                <strong style="font-weight: bold;">Código Postal: </strong>'.$data['Codigo_Postal'].'<br>
                                <strong style="font-weight: bold;">Tipo: </strong>'.$data['Tipo'].'<br>
                                <strong style="font-weight: bold;">Precio: </strong>'.$data['Precio'].'<br>
                              </p>
                              <button type="button" class="btn delete_avaliables" id_available="'.$d_query['id'].'">Eliminar</button>';
                    $html .= '</div>';
                }
            }

            $html .= '<script>
                    $(".delete_avaliables").on("click", function(){
                        $.ajax({
                          type: "POST",
                          url: "php/functions.php",
                          data:{
                            "type": "delete_available_goods",
                            "id_available": $(this).attr("id_available")
                          },
                          dataType: "json",
                          success: function(data){
                            if(data.response == "correct"){
                                alert(data.info);
                                $("#divResultadosBusqueda2").html(data.html);
                            }else{
                                alert(data.info);
                            }
                          },
                          error: function(data){
                            console.log("Error eliminando Bienes disponibles");
                          }
                        });
                    });
                  </script>';

            echo json_encode(array(
                'response' => 'correct',
                'info' => 'Bien eliminado correctamente',
                'html' => $html
            ));
        }
    }
    if($_POST['type'] == 'download_exel'){
        $spreadsheet = new SpreadSheet();
        $spreadsheet->getProperties()->setCreator('Suplos')->setTitle('Reporte');

        $spreadsheet->setActiveSheetIndex(0);
        $hojaActiva = $spreadsheet->getActiveSheet();
        
        $hojaActiva->getColumnDimension('A')->setWidth(40);
        $hojaActiva->getColumnDimension('B')->setWidth(15);
        $hojaActiva->getColumnDimension('C')->setWidth(20);
        $hojaActiva->getColumnDimension('D')->setWidth(20);
        $hojaActiva->getColumnDimension('E')->setWidth(20);

        $hojaActiva->setCellValue('A1', 'Dirección')->setCellValue('B1', 'Ciudad');
        $hojaActiva->setCellValue('C1', 'Teléfono')->setCellValue('D1', 'Código Postal');
        $hojaActiva->setCellValue('E1', 'Tipo')->setCellValue('F1', 'Precio');

        if($_POST['reporte'] == 'Mis bienes'){
          $i = 2;
          foreach($avaliable_goods as $data){
            $query = "SELECT `id`, `id_available` FROM `available_goods_saved` WHERE `id_available` = $data[Id]";
            $s_query = mysqli_query($con, $query);
            $c_query = mysqli_num_rows($s_query);
            if($c_query>0){
              if(isset($_POST['ciudad']) && isset($_POST['tipo'])){
                if($_POST['ciudad'] == $data['Ciudad'] && $_POST['tipo'] == $data['Tipo']){
                  $hojaActiva->setCellValue('A'.$i, $data['Direccion'])->setCellValue('B'.$i, $data['Ciudad']);
                  $hojaActiva->setCellValue('C'.$i, $data['Telefono'])->setCellValue('D'.$i, $data['Codigo_Postal']);
                  $hojaActiva->setCellValue('E'.$i, $data['Tipo'])->setCellValue('F'.$i, $data['Precio']);
                  $i++;
                }
              }elseif(isset($_POST['ciudad']) && !isset($_POST['tipo'])){
                if($_POST['ciudad'] == $data['Ciudad']){
                  $hojaActiva->setCellValue('A'.$i, $data['Direccion'])->setCellValue('B'.$i, $data['Ciudad']);
                  $hojaActiva->setCellValue('C'.$i, $data['Telefono'])->setCellValue('D'.$i, $data['Codigo_Postal']);
                  $hojaActiva->setCellValue('E'.$i, $data['Tipo'])->setCellValue('F'.$i, $data['Precio']);
                  $i++;
                }
              }elseif(!isset($_POST['ciudad']) && isset($_POST['tipo'])){
                if($_POST['tipo'] == $data['Tipo']){
                  $hojaActiva->setCellValue('A'.$i, $data['Direccion'])->setCellValue('B'.$i, $data['Ciudad']);
                  $hojaActiva->setCellValue('C'.$i, $data['Telefono'])->setCellValue('D'.$i, $data['Codigo_Postal']);
                  $hojaActiva->setCellValue('E'.$i, $data['Tipo'])->setCellValue('F'.$i, $data['Precio']);
                  $i++;
                }
              }else{
                $hojaActiva->setCellValue('A'.$i, $data['Direccion'])->setCellValue('B'.$i, $data['Ciudad']);
                $hojaActiva->setCellValue('C'.$i, $data['Telefono'])->setCellValue('D'.$i, $data['Codigo_Postal']);
                $hojaActiva->setCellValue('E'.$i, $data['Tipo'])->setCellValue('F'.$i, $data['Precio']);
                $i++;
              }
            }
          }
            
        }else{
          $i = 2;
          foreach($avaliable_goods as $data){
            if(isset($_POST['ciudad']) && isset($_POST['tipo'])){
              if($_POST['ciudad'] == $data['Ciudad'] && $_POST['tipo'] == $data['Tipo']){
                $hojaActiva->setCellValue('A'.$i, $data['Direccion'])->setCellValue('B'.$i, $data['Ciudad']);
                $hojaActiva->setCellValue('C'.$i, $data['Telefono'])->setCellValue('D'.$i, $data['Codigo_Postal']);
                $hojaActiva->setCellValue('E'.$i, $data['Tipo'])->setCellValue('F'.$i, $data['Precio']);
                $i++;
              }
            }elseif(isset($_POST['ciudad']) && !isset($_POST['tipo'])){
              if($_POST['ciudad'] == $data['Ciudad']){
                $hojaActiva->setCellValue('A'.$i, $data['Direccion'])->setCellValue('B'.$i, $data['Ciudad']);
                $hojaActiva->setCellValue('C'.$i, $data['Telefono'])->setCellValue('D'.$i, $data['Codigo_Postal']);
                $hojaActiva->setCellValue('E'.$i, $data['Tipo'])->setCellValue('F'.$i, $data['Precio']);
                $i++;
              }
            }elseif(!isset($_POST['ciudad']) && isset($_POST['tipo'])){
              if($_POST['tipo'] == $data['Tipo']){
                $hojaActiva->setCellValue('A'.$i, $data['Direccion'])->setCellValue('B'.$i, $data['Ciudad']);
                $hojaActiva->setCellValue('C'.$i, $data['Telefono'])->setCellValue('D'.$i, $data['Codigo_Postal']);
                $hojaActiva->setCellValue('E'.$i, $data['Tipo'])->setCellValue('F'.$i, $data['Precio']);
                $i++;
              }
            }else{
              $hojaActiva->setCellValue('A'.$i, $data['Direccion'])->setCellValue('B'.$i, $data['Ciudad']);
              $hojaActiva->setCellValue('C'.$i, $data['Telefono'])->setCellValue('D'.$i, $data['Codigo_Postal']);
              $hojaActiva->setCellValue('E'.$i, $data['Tipo'])->setCellValue('F'.$i, $data['Precio']);
              $i++;
            }
          }
        }

        //$spreadsheet->getActiveSheet()->getStyle('A')->setWrapText(true);

        //header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        //header('Content-Disposition: attachment;filename="prueba.xlsx"');
        //header('Cache-Control: max-age=0');
        //  
        //$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        //$writer->save('php://output');
        //exit;
        $writer = new Xlsx($spreadsheet);
        $writer->save('Reporte.xlsx');

        echo json_encode(array(
            'response' => 'correct'
        ));
        
    }
?>