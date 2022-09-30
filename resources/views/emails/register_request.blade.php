<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

</head>
<body>
   <div>
   
    <p>Hola {{$request->petitioner->profile->fullName}}</p> 
    <p>Tu solicitud se ha generado con exito</p>
    <table>
        <td><tr>Solicitud N°</tr><tr>{{$request->id}}</tr></td>
        <td><tr>Solicitud N°</tr><tr>{{$request->id}}</tr></td>

    </table>


   </div>
</body>
</html>