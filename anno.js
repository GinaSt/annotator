
var canvas = document.getElementById("canvas");
var ctx = canvas.getContext('2d');
trackTransforms(ctx);


ctx.font = "20px Helvetica";
ctx.fillStyle = "gray";
ctx.textAlign = "center";
ctx.fillText("Please log in to load first image",canvas.width/2, canvas.height/2);

var c = document.getElementById("canvas_small");
var cx = c.getContext('2d');
imw=550;
//imh=700;
  
//preload(imageSourceArray);  // preloader===> preload all images in the database!!!!

var lastX=canvas.width/2, lastY=canvas.height/2;
var dragStart,dragged;

loggedin=0; // flag to verify if a user is logged in or not



function start_canvas(){
    canvas.addEventListener('mousemove', function(evt) {
        var mousePos = getMousePos(canvas, evt);
      }, false);

    canvas.addEventListener('mousedown',pan_down,false);
    canvas.addEventListener('mousemove',pan_move,false);
    canvas.addEventListener('mouseup', pan_up,false);
    canvas.addEventListener('DOMMouseScroll',  handleScroll,false);
    canvas.addEventListener('mousewheel',  handleScroll,false);
    canvas.addEventListener('MozMousePixelScroll',  handleScroll,false);


    document.getElementById('pan').addEventListener('click',pan);
    document.getElementById('draw').addEventListener('click',draw);
    document.getElementById("pan").style.background = "grey";
    document.getElementById("pan").style.color = "white";

    document.getElementById('clear').addEventListener('click', function() {
         cx.clearRect(0, 0, c.width, c.height); Boundheight=0;
    Boundwidth=0;  clear_canvas();
      }, false);


    document.getElementById('clear_last').addEventListener('click', function() {
        cx.clearRect(0, 0, c.width, c.height); 
        Boundheight=0;
        Boundwidth=0;  
        if (points.length!=0){points=new Array(); 
          redraw();
        reDrawPoint();
        }
        else{
        all_polygons.pop(); redraw();
        reDrawPoint();}
      }, false);
}


$(document).ready(function() {
    $('#login_form').submit(function(e) {
        data = $('#login_form').serialize();
        $.ajax({
            url: 'login_try.php',
            type: 'POST',
            dataType: 'json',
            data: data,
            beforeSend: function(){
                $("#wait").css("display", "block");},
            complete: function(){
                  
                 $("#wait").css("display", "none"); }
                })
        .done(function(response) {
            //$("#wait").css("display", "block");
            if (response.data =="empty") { alert('Email, first and last name fields are required'); } 
            else if (response.data == "none"){ 
                     alert("You have annotated all the available images. To begin the process again you will have to login with a different e-mail.");}
            else if (response.data == "invalid"){ alert("Invalid e-mail address")}
            else {
                
                //alert(response.user);      
                $('#id').html("<strong>ID:</strong> "+response.id);
                $('#name').html("<strong>Name:</strong> "+response.name);
                $('#collection').html("<strong>Collection:</strong> "+response.collection);
                $('#era').html("<strong>Era:</strong> "+response.era);
                currentImage=response.name
                currentImage_id=response.id
                currentUser=response.user
                loggedin=1;
                start_canvas();

                imageArray=response.tabletarray
                console.log("Array: "+imageArray);
                //console.log(imageSourceArray)
                //console.log("User: "+currentUser);
                make_base();
                
                $('#form_div').toggle();//fadeOut('400');
                $('#info').toggle();//fadeIn('400');
            }; 
            //$("#wait").css("display", "none"); 
          
        })     
        e.preventDefault();
    });
});


$(document).ready(function(){
    $("#next").on("click", function () {
        if (loggedin==1){
        //console.log("Current User");
        cx.clearRect(0, 0, c.width, c.height);
        $("#wait").css("display", "block")
        $.post('next_load.php', {'user':currentUser, 'tablet': currentImage_id}, function(data){
        $("#wait").css("display", "none")
        if (data.data == "none"){ alert("You have annotated all the available images. To begin the process again you will have to login with a different e-mail.");}
        else{
            $('#id').html("<strong>ID:</strong> "+data.id);
            $('#name').html("<strong>Name:</strong> "+data.name);
            $('#collection').html("<strong>Collection:</strong> "+data.collection);
            $('#era').html("<strong>Era:</strong> "+data.era);
            currentImage=data.name
            currentImage_id=data.id
            //console.log("Next:"+currentImage);
            remake_base(); } } , 'json');}
          }); }); 



$(document).ready(function(){
  $("#save").on("click", function () {
    if(Boundheight===0 && Boundwidth===0)
        alert('You have not defined a polygon!');
    else if (!$.trim($("#num_anno").val())) {
        alert('You have not inserted an index number!');}
    else if (!$("input[name='quality']:checked").val()) {
        alert('You have to rate the quality of the sign!');
    }
    else{
      var imageData = cx.getImageData(0, 0, Boundwidth, Boundheight);
      var newCanvas = document.createElement("canvas");
      newCanvas.width = Boundwidth;
      newCanvas.height = Boundheight;
      newCanvas.getContext("2d").putImageData(imageData, 0, 0);
      var canvasData = newCanvas.toDataURL("image/img",1);
      // var ajax = new XMLHttpRequest();
      // ajax.open("POST",'testSave.php',false);
      // ajax.setRequestHeader('Content-Type', 'application/upload');
      // ajax.send(canvasData);
      $.post('savesign.php', {'quality': $("[name='quality']:checked").val(),'data': canvasData,'anno':document.getElementById("num_anno").value, 'comment':document.getElementById("comment").value, 'wedges':document.getElementById("num_wedges").value,'user':currentUser, 'tablet': currentImage_id}, function() {
        alert( "Annotated sign is saved!" );})

      cx.clearRect(0, 0, c.width, c.height);

      document.getElementById("num_anno").value = ''
      document.getElementById("num_wedges").value = ''
      document.getElementById("comment").value = ''
      document.getElementById('rd1').checked = false;
      document.getElementById('rd2').checked = false;
      document.getElementById('rd3').checked = false;
      document.getElementById('rd4').checked = false;
      document.getElementById('rd5').checked = false;
      }
    });
  });






    
//=============FUNCTIONS==========================


function pan_down(evt){
    document.body.style.mozUserSelect = document.body.style.webkitUserSelect = document.body.style.userSelect = 'none';
    var rect = canvas.getBoundingClientRect();
    lastX=Math.round((evt.clientX-rect.left)/(rect.right-rect.left)*canvas.width),
    lastY=Math.round((evt.clientY-rect.top)/(rect.bottom-rect.top)*canvas.height)
    dragStart = ctx.transformedPoint(lastX,lastY);
    dragged = false;
}


function pan_move(evt){
    var rect = canvas.getBoundingClientRect();

    lastX=Math.round((evt.clientX-rect.left)/(rect.right-rect.left)*canvas.width),
    lastY=Math.round((evt.clientY-rect.top)/(rect.bottom-rect.top)*canvas.height)
    dragged = true;
    
    if (dragStart){
      var pt = ctx.transformedPoint(lastX,lastY);
      ctx.translate(pt.x-dragStart.x,pt.y-dragStart.y);
      lastX=pt.x
      lastY=pt.y
      redraw(); 
      reDrawPoint();
      originx=originx-Math.round(pt.x-dragStart.x)
      originy=originy-Math.round(pt.y-dragStart.y)
    }   
}


function pan_up(evt){
    dragStart = null;
    //if (!dragged) zoom(evt.shiftKey ? -1 : 1 );
}


function pan(){
    canvas.removeEventListener("mousedown", drawPoint, false); //remove the ability to draw 
    canvas.addEventListener('mousedown',pan_down,false);
    canvas.addEventListener('mousemove',pan_move,false);
    canvas.addEventListener('mouseup', pan_up,false);
    canvas.style.cursor = "move";
    document.getElementById("pan").style.color = "white";
    document.getElementById("draw").style.color = "black";
    document.getElementById("pan").style.background = "grey";
    document.getElementById("draw").style.background = "white";
  }


function draw(){
    canvas.removeEventListener('mousedown',pan_down,false); //remove the ability to pan
    canvas.removeEventListener('mousemove',pan_move,false);
    canvas.removeEventListener('mouseup', pan_up,false);
    canvas.addEventListener("mousedown", drawPoint, false);
    canvas.style.cursor = "crosshair";
    document.getElementById("draw").style.color = "white";
    document.getElementById("pan").style.color = "black";
    document.getElementById("draw").style.background = "grey";
    document.getElementById("pan").style.background = "white"; 
}
   



    
function clear_canvas(){
  points= new Array(); 
  all_polygons=new Array();
  ctx.save();
  ctx.setTransform(1, 0, 0, 1, 0, 0);
  ctx.clearRect(0, 0, canvas.width, canvas.height);
  ctx.restore();
  ctx.drawImage(img,20,20,imw,img.height * (imw/img.width));
  document.getElementById("num_anno").value = ''
  document.getElementById("num_wedges").value = ''
  document.getElementById("comment").value = ''
  document.getElementById('rd1').checked = false;
  document.getElementById('rd2').checked = false;
  document.getElementById('rd3').checked = false;
  document.getElementById('rd4').checked = false;
  document.getElementById('rd5').checked = false;
}



// function make_base(){
//   //
//   ctx.setTransform(1, 0, 0, 1, 0, 0);
//   ctx.clearRect(0, 0, canvas.width, canvas.height);
//   scale = 1;
//   originx = 0;
//   originy = 0;
//   Boundheight=0;
//   Boundwidth=0;
//   // Create empty array to store the user's clicks
//   points=new Array();
//   all_polygons=new Array();
//   console.log("current image:" +currentImage)
//     for (i = 0; i < image_names.length; i++){
//       if (currentImage===image_names[i]){
//         img=images[i];
//         ctx.drawImage(img,20,20,imw,img.height * (imw/img.width));
//         img.onload = function(){ $("#wait").css("display", "block"); 
//             ctx.drawImage(img,20,20,imw,img.height * (imw/img.width));
//             $("#wait").css("display", "none"); }
//         //img.src=imageSourceArray[i];
//         //console.log("image loaded");
//         //console.log("success: "+currentImage+" == "+ image_names[i])
//         break;}
//   else {console.log("Searching for image!!!!")} }
  
//   //img = images[Math.floor(Math.random()*images.length)];
//   //console.log(img)

  
//   //
// }


// function preload(imageSourceArray){
//     images = [];
//     image_names=[];
//     for (i = 0; i < imageSourceArray.length; i++){
//         image_names[i]=imageSourceArray[i].substring(7, imageSourceArray[i].indexOf('.png'))
//         console.log(image_names[i])
//         images[i] = new Image();
//         images[i].onload = function(){ console.log("image loaded");}
//         images[i].src = imageSourceArray[i];
//       }
// }




function make_base(){
  ctx.setTransform(1, 0, 0, 1, 0, 0);
  ctx.clearRect(0, 0, canvas.width, canvas.height);
  scale = 1;
  originx = 0;
  originy = 0;
  Boundheight=0;
  Boundwidth=0;
  // Create empty array to store the user's clicks
  points=new Array();
  all_polygons=new Array();
  console.log("current image:" +currentImage);
  img= new Image();
  //ctx.drawImage(img,20,20,imw,img.height * (imw/img.width));
  img.onload = function(){ ctx.drawImage(img,20,20,imw,img.height * (imw/img.width)); console.log('first loaded') }
  img.src= "images/"+ currentImage +".png"
  preload(imageArray);}

function remake_base(){
  ctx.setTransform(1, 0, 0, 1, 0, 0);
  ctx.clearRect(0, 0, canvas.width, canvas.height);
  scale = 1;
  originx = 0;
  originy = 0;
  Boundheight=0;
  Boundwidth=0;
  // Create empty array to store the user's clicks
  points=new Array();
  all_polygons=new Array();
  console.log("current image:" +currentImage);
  for (i = 0; i < imageArray.length; i++){
      if (currentImage===image_names[i]){
        img=images[i];
        ctx.drawImage(img,20,20,imw,img.height * (imw/img.width));
        img.onload = function(){ $("#wait").css("display", "block"); 
        ctx.drawImage(img,20,20,imw,img.height * (imw/img.width));
        $("#wait").css("display", "none"); }

        //img.src=imageSourceArray[i];
        //console.log("image loaded");
        //console.log("success: "+currentImage+" == "+ image_names[i])
        break;}
  else {console.log("Searching for image!!!!")} }

  }

  // if (loaded.indexOf(currentImage)>-1){
  //   img=images[loaded.indexOf(currentImage)]
  //   ctx.drawImage(img,20,20,imw,img.height * (imw/img.width));
  //   console.log("ttttt")}

  // else{
  //   img= new Array();
  //   img.onload = function(){ $("#wait").css("display", "block"); 
  //           ctx.drawImage(img,20,20,imw,img.height * (imw/img.width));
  //           $("#wait").css("display", "none"); }

  //   img.src= "images/"+ currentImage +".png"
  //   console.log(img.src)


  // }
    
  
  //img = images[Math.floor(Math.random()*images.length)];
  //console.log(img)

  
  //



// function preload(imageSourceArray){
//     images = [];
//     image_names=[];
//     for (i = 0; i < imageSourceArray.length; i++){
//         image_names[i]=imageSourceArray[i].substring(7, imageSourceArray[i].indexOf('.png'))
//         //console.log(image_names[i])
//         images[i] = new Image();
//         images[i].onload = function(){ console.log("image loaded");}
//         images[i].src = imageSourceArray[i];
//       }
// }


function preload(imageArray){
    images = [];
    image_names=imageArray;
    for (i = 0; i < imageArray.length; i++){
        images[i] = new Image();
        images[i].onload = function(){ console.log("image loaded");}
        images[i].src = "images/"+ imageArray[i] +".png";
      }
}



function getMousePos(canvas, evt) {
    var rect = canvas.getBoundingClientRect();
    MousePos={
    x: Math.round((evt.clientX-rect.left)/(rect.right-rect.left)*canvas.width),
    y: Math.round((evt.clientY-rect.top)/(rect.bottom-rect.top)*canvas.height)
    };
    //ShowPoints();
    return ctx.transformedPoint(MousePos.x,MousePos.y);
}


function drawLine(){
    ctx.beginPath();
    ctx.moveTo(points[points.length-2][0],points[points.length-2][1]);
    ctx.lineTo(points[points.length-1][0],points[points.length-1][1]);
    ctx.stroke();
    ctx.closePath();
}


function drawPoint(evt){   
    var mousePos = getMousePos(canvas, evt);
    var point = [mousePos.x,mousePos.y]
    ctx.fillStyle="DeepPink";
    ctx.strokeStyle="DarkTurquoise";
    ctx.lineWidth=2/scale;
    ctx.beginPath();
    ctx.arc(mousePos.x, mousePos.y, 2/scale, 0, 2 * Math.PI, false);
    ctx.fill();
    ctx.closePath();  
    
    if (points.length==0){
        points[points.length]=point;
        //ShowPoints();
        }
    else if (Math.abs(point[0]-points[0][0])<10/scale && Math.abs(point[1]-points[0][1])<10/scale && points.length>2){

        ctx.save();
        temp=scale;
        clipIt();
        ctx.setTransform(1, 0, 0, 1, 0, 0);
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.scale(scale,scale)

        ctx.translate(-minX,-minY);

        ctx.moveTo(points[0][0],points[0][1]);
        ctx.beginPath();
        for (var i=0;i<points.length;i++){
            ctx.lineTo(points[i][0],points[i][1]);
            }
        ctx.lineTo(points[0][0],points[0][1]);
        ctx.closePath();
        ctx.clip();
        ctx.drawImage(img,20,20,imw,img.height * (imw/img.width));
        ctx.restore();
        drawSmallCanvas();
        ctx.save();
        ctx.setTransform(1, 0, 0, 1, 0, 0);
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.restore();
        scale=temp;
        ctx.drawImage(img,20,20,imw,img.height * (imw/img.width));
        save_polygon=points;
        all_polygons[all_polygons.length]=points;
        points=new Array();
        reDrawPoint(); 
        }
    else {
        points[points.length]=point;
        drawLine()
        }
    
}



// function ShowPoints(){
//     var txt=document.getElementById("txt");
//     //txt.value=points[points.length-1][0]+ ","+points[points.length-1][1]
//     txt.value=MousePos.x+ ","+MousePos.y   
// }


function clipIt(){

  // // calculate the size of the user's clipping area

  minX=10000;
  minY=10000;
  maxX=-10000;
  maxY=-10000;
  for(var i=0;i<points.length;i++){
    var p=points[i];
    if(p[0]<minX){minX=p[0];}
    if(p[1]<minY){minY=p[1];}
    if(p[0]>maxX){maxX=p[0];}
    if(p[1]>maxY){maxY=p[1];}
  }
  Boundwidth=maxX-minX;
  Boundheight=maxY-minY;
  // console.log("minX: "+minX+", minY: "+minY)
  // console.log("maxX: "+maxX+",maxY: "+maxY)
  // console.log("Boundwidth: "+Boundwidth+",Boundheight: "+Boundheight)
  // console.log("c.width: "+c.width+",c.height: "+c.height)

  if (Boundwidth>=Boundheight) {scale=c.width/Boundwidth}
    else {scale=c.height/Boundheight}

  Boundwidth*=scale
  Boundheight*=scale

  
}

function drawSmallCanvas(){
  cx.clearRect(0, 0, c.width, c.height); 
  cx.drawImage(canvas, 0,0,Boundwidth,Boundheight, 0,0,Boundwidth,Boundheight);//Boundwidth,Boundheight);

  //
}



function redraw(){
      ctx.save();
      ctx.setTransform(1,0,0,1,0,0);
      ctx.clearRect(0,0,canvas.width,canvas.height);
      ctx.restore();
      ctx.drawImage(img,20,20,imw,img.height * (imw/img.width));    
      }
   

function reDrawPoint(){   
  if (all_polygons.length!=0){
  for (var i=0;i<all_polygons.length;i++){
    var closed_polygon=all_polygons[i];
    //console.log(closed_polygon.length)
    ctx.moveTo(closed_polygon[0][0],closed_polygon[0][1]);
    ctx.beginPath();
    for (var j=0;j<closed_polygon.length;j++){
    ctx.lineTo(closed_polygon[j][0],closed_polygon[j][1]);
    }
    ctx.lineTo(closed_polygon[0][0],closed_polygon[0][1]);
    ctx.stroke();
    ctx.closePath();
    ctx.fillStyle = "rgba(0, 0, 0, 0.2)";
    ctx.fill();}
  }
    ctx.fillStyle="DeepPink";
    ctx.strokeStyle="DarkTurquoise ";
    ctx.lineWidth=2/scale;

    if (points.length==1){
    ctx.beginPath();
    ctx.arc(points[0][0],points[0][1], 2/scale, 0, 2 * Math.PI, false);
    ctx.fill();
    ctx.closePath();
    }
    else if (points.length>1){
      ctx.beginPath();
        ctx.moveTo(points[0][0],points[0][1]);
        for (var i=1;i<points.length;i++){
        ctx.lineTo(points[i][0],points[i][1]);
        ctx.stroke();}
        ctx.closePath();
        for (var i=0;i<points.length;i++){
          ctx.beginPath();
          ctx.arc(points[i][0],points[i][1], 2/scale, 0, 2 * Math.PI, false);
          ctx.fill();
          ctx.closePath();
        }        
      } 
} 


function zoom(delta,event){
    var rect = canvas.getBoundingClientRect();


    mousePos={
          x: Math.round((event.clientX-rect.left)/(rect.right-rect.left)*canvas.width),
          y: Math.round((event.clientY-rect.top)/(rect.bottom-rect.top)*canvas.height)
          };

    var factor = Math.pow(1 + Math.abs(delta)/2 , delta > 0 ? 1 : -1);

    ctx.translate(originx,originy);


    ctx.scale(factor,factor);
    ctx.translate(
        -( mousePos.x / scale + originx - mousePos.x / ( scale * factor ) ),
        -( mousePos.y / scale + originy - mousePos.y / ( scale * factor ) )
    );

    originx = ( mousePos.x / scale + originx - mousePos.x / ( scale * factor ) );
    originy = ( mousePos.y / scale + originy - mousePos.y / ( scale * factor ) );
    scale *= factor;

      //console.log("originx: "+originx+"originy"+originy)
    
    redraw();
    if (points){reDrawPoint();}
    }


function handleScroll(event){
    var delta = event.wheelDelta ? event.wheelDelta/20 : event.detail ? -event.detail : 0;
    //console.log("Delta: "+delta)
     //console.log("Scale: "+scale)
    if (delta){
      if (delta>0 && scale>=8) {} 
      else if (delta<0 && scale<=0.7) {}
      else if (delta>0){
        delta=0.15
        zoom(delta,event); }
      else if (delta<0){
        delta=-0.15
        zoom(delta,event);}
    }
    return event.preventDefault() && false;
  }



  function trackTransforms(ctx){
    var svg = document.createElementNS("http://www.w3.org/2000/svg",'svg');
    var xform = svg.createSVGMatrix();
    ctx.getTransform = function(){ return xform; };
    
    var savedTransforms = [];
    var save = ctx.save;
    ctx.save = function(){
      savedTransforms.push(xform.translate(0,0));
      return save.call(ctx);
    };
    var restore = ctx.restore;
    ctx.restore = function(){
      xform = savedTransforms.pop();
      return restore.call(ctx);
    };

    var scale = ctx.scale;
    ctx.scale = function(sx,sy){
      xform = xform.scaleNonUniform(sx,sy);
      return scale.call(ctx,sx,sy);
    };
    var rotate = ctx.rotate;
    ctx.rotate = function(radians){
      xform = xform.rotate(radians*180/Math.PI);
      return rotate.call(ctx,radians);
    };
    var translate = ctx.translate;
    ctx.translate = function(dx,dy){
      xform = xform.translate(dx,dy);
      return translate.call(ctx,dx,dy);
    };
    var transform = ctx.transform;
    ctx.transform = function(a,b,c,d,e,f){
      var m2 = svg.createSVGMatrix();
      m2.a=a; m2.b=b; m2.c=c; m2.d=d; m2.e=e; m2.f=f;
      xform = xform.multiply(m2);
      return transform.call(ctx,a,b,c,d,e,f);
    };
    var setTransform = ctx.setTransform;
    ctx.setTransform = function(a,b,c,d,e,f){
      xform.a = a;
      xform.b = b;
      xform.c = c;
      xform.d = d;
      xform.e = e;
      xform.f = f;
      return setTransform.call(ctx,a,b,c,d,e,f);
    };
    var pt  = svg.createSVGPoint();
    ctx.transformedPoint = function(x,y){
      pt.x=x; pt.y=y;
      return pt.matrixTransform(xform.inverse());
    }

  }
