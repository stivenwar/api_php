
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
.addElement{
    display: flex;
    flex-direction: column;
    justify-content: center;
    width: 50%;
    margin: 0 auto;
}

.sendProduct{
    width: 30%;
    height: 5%;
}
.btn-delete-supplier{
 display: flex;
 justify-content: right;
}
.btn-delete button, .btn-delete-supplier button{
    background-color: brown;
    height: 20px;
    margin-left: 10px;
      border-radius: 5px;
      color: whitesmoke;
}
.btn-delete button:hover{
    background-color: red;
   
}

.btn {
    background-color:darkgreen;
    color: whitesmoke;
    padding: 10px 0px 10px 0px;
    margin-top: 15px;

    border-radius: 5px;
}
.btn:hover{
 background-color: green;
}
    </style>
</head>
<body>

<?php
if (isset($_GET['success'])) {
    echo "<div class='success' style='color:green;'>Operación exitosa!</div>";
} elseif (isset($_GET['error'])) {
    echo "<div class='fail' style='color:red;'>Hubo un error en la operación.</div>";
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

    setTimeout(()=>{
        $(".success").hide();
    },5000)
    $(".loader").show();

    const jsonData = [];
        getList();
   

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
    const display = `<div id='${(jsonData.length-1)}' class="btn-delete"><p>✔ ${name}<button type="button" class='deleteProduct' data-id='new-${(jsonData.length-1)}'>X</button></p></div> `;
    $(`#list-${supplierId}`).append(display);

    // Limpiar input
    $(`#name-${supplierId}`).val("").focus();

    console.log(jsonData);

    localStorage.removeItem(JSON.stringify(jsonData));

    
});
$(document).on("click",".deleteProduct", function(){
    const productId = $(this).data("id");
    if (typeof productId === "string") {
        let id = parseInt(productId.substring(productId.length-1));
        console.log(id);
        $(`#${id}`).remove();
        jsonData.splice(id,1);
        console.log(jsonData);
        
        
    }
    if(typeof productId === "number"){
         $.ajax({
                    type:"DELETE",
                    url:"/delete-product",
                    data:JSON.stringify({id_product:productId}),
                    success: function(result){
                     console.log("Dato eliminado correctamente",result);
                     getList();
                    },
                    error: function(error){
                        console.log("error",error);
                        
                    }
                   
                })
    }
    
    
    



})
 
       
    $(document).on("click",".deleteSupplier", function(){
            const supplierId = $(this).data("id");
            console.log(supplierId);

            let responce = confirm("Vas a eliminar todos los productos del proveedor estas seguro?");

            if (responce) {
                $.ajax({
                    type:"DELETE",
                    url:"/delete-supplier",
                    data:JSON.stringify({id_supplier:supplierId}),
                    success: function(result){
                     console.log("Datos enviados correctamente",result);
                     getList();
                   
                    },
                    error: function(error){
                        console.log("error",error);
                        
                    }
                   
                })
            }

    })
    // 🚀 Enviar datos
    $(document).on("click", ".sendProduct", function () {
      
        if(jsonData.length === 0){
            return alert("no has ingresado ningun producto");
        }

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

  function getList(){

    $(".result").empty();
    $(".sendProduct").remove();

     $.get("/getList", function (response) {
        console.log(response);
        const {data, success} = response;
        
        data.registros.forEach(function (element) {
            const supplierId = element.id_suppliers;
            const product  = element.products;

      $(".result").append(`
        <div class="supplier-block" data-supplier="${supplierId}">
        <div class="btn-delete-supplier"><button type="button" class='deleteSupplier' data-id='${supplierId}'>X</button></div>
        <h3>${element.supplier}</h3>
        <div class="added-items" id="list-${supplierId}"></div>
        <div class='addElement'>
        <input type='text' name='name-${supplierId}' id='name-${supplierId}' placeholder='Producto' size='10'>
        <button type="button" class='addProduct btn' data-id='${supplierId}'>Añadir</button>

        </div>
    </div>
        `);
      
        product.forEach(function(product){

            if (product.id_supplier === supplierId) {
                // console.log(product.id_supplier,supplierId);
                
                $(`#list-${supplierId}`).append(`<div class="btn-delete"><p>✔ ${product.name_product}<button type="button" class='deleteProduct' data-id='${product.id_product}'>X</button></p></div> `)

            }
        })
     
        });
        $("body").append(`  <button type="button" class='sendProduct btn '>Enviar</button>`)

        $(".loader").hide();
    }).fail(function(jqXHR, textStatus, errorThrown) {
    console.error('Error en la petición:', textStatus, errorThrown);
    alert('Ocurrió un error: ' + errorThrown);
    })
  }
</script>


  

</body>
</html>




