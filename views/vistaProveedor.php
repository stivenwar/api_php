
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Agregar Proveedor</title>
    <script src="/js/jquery-3.7.1.js"></script>

    <style>
      .result {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 10px;
  


}

.supplier-block {
  padding: 10px;
  border: 1px solid #ccc;
  min-height: 150px;
  box-sizing: border-box;
  text-align: center;

}

.sendProduct{
    width: 30%;
    height: 30%;
}
    </style>
</head>
<body>

<?php
if (isset($_GET['success'])) {
    echo "<p style='color:green;'>Operación exitosa!</p>";
} elseif (isset($_GET['error'])) {
    echo "<p style='color:red;'>Hubo un error en la operación.</p>";
}
?>
    <h1>Agregar Nuevo Proveedor</h1>
    <form method="POST" action="/createList">
        <label for="supplier">Proveedor:</label><br>
        <input type="text" id="supplier" name="supplier" required><br><br>

        <label for="description">Descripción:</label><br>
        <textarea id="description" name="description" rows="4" cols="50" required></textarea><br><br>

        <button type="submit">Guardar</button>
    </form>
    <br>
    <br>
    <button class="btn">send json</button>
    <div class="loader" style="display: none;">Cargando datos...</div>
      <h1>Lista de Compra</h1>
    <div class="result">

    </div>  


    <script>
 
        
$(document).ready(function () {

    
    $(".loader").show();

    const jsonData = [];

    $.get("/getList", function (data) {
        console.log(data);
        
        
        data.registros.forEach(function (element) {
            const supplierId = element.id_suppliers;
            const product  = element.products;


      $(".result").append(`
        <div class="supplier-block" data-supplier="${supplierId}">
        <h3>${element.supplier}</h3>
        <div class="added-items" id="list-${supplierId}"></div>
        <div class='addElement'>
        <input type='text' name='name-${supplierId}' id='name-${supplierId}' placeholder='Producto' size='10'>
        <button type="button" class='addProduct' data-id='${supplierId}'>Añadir</button>
        </div>
    </div>
        `);
      
        product.forEach(function(product){

            if (product.id_supplier === supplierId) {
                // console.log(product.id_supplier,supplierId);
                
                $(`#list-${supplierId}`).append(`<p>✔ ${product.name_product}</p>`)

            }
        })
     
        });
        $(".result").append(`  <button type="button" class='sendProduct'>Enviar</button>`)

        $(".loader").hide();
    });

    // 👉 Añadir producto
    $(document).on("click", ".addProduct", function () {
          const supplierId = $(this).data("id");
          console.log(supplierId);
          
    const name = $(`#name-${supplierId}`).val().trim();

    if (!name) {
        alert("Escribe un nombre antes de añadir");
        return;
    }

    // Evitar duplicados por proveedor + nombre
    const exists = jsonData.some(
        item => item.supplierId === supplierId && item.name === name
    );
    if (exists) {
        alert("Este producto ya fue añadido.");
        return;
    }

    // Añadir al array
    jsonData.push({ supplierId: supplierId, name: name });

    // Mostrar en pantalla
    const display = `<p>✔ ${name}</p>`;
    $(`#list-${supplierId}`).append(display);

    // Limpiar input
    $(`#name-${supplierId}`).val("").focus();

    console.log(jsonData);

    
});
       

    // 🚀 Enviar datos
    $(document).on("click", ".sendProduct", function () {
      

        $.post("/createProducts", { data: JSON.stringify(jsonData)}, function (response) {
            console.log(response);
            
            console.log("Datos enviados correctamente");
          
          
        });
    });

    $(".btn").on("click",function(){
        let json = [
        { "id_product":1, "quantity": 3} ,
  {
    "id_product":1,
    "quantity": 3
  }  ,
  {
    "id_product":1,
    "quantity": 3
  }  ]
        sendJson(json);
    });
           function sendJson(json){
            $.post("/sendQuantity", JSON.stringify(json) ,function (responce){
console.log(responce);

  });
        }
});
</script>


  

</body>
</html>




