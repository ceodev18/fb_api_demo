<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title></title>
    <script src="https://ajax.aspnetcdn.com/ajax/jQuery/jquery-3.2.1.min.js"></script>
  </head>
  <body>
    <h1>Uso API Facebook</h1>
    <div id="fb-root"></div>
    <div class="">
      <label width="300">Inicio de Sesión</label><br>
      <button onclick="javascript:login();">Login Facebook</button><br>
      <div id="contenido" style="padding:50px"></div>
    </div>
    <div class="">
      <label width="300">Postear en mi muro</label><br>
      <input type="text" placeholder="Inserte el texto" id="i_post"><br>
      <input type="text" placeholder="Ingrese el tiempo min." id="i_timePostMine"><br>
      <button onclick="post()">Post</button>
    </div><br>

    <div class="">
      <label width="300">Postear en Grupo</label><br>
      <input type="text" name="" value="" placeholder="Ingrese Contenido" id="i_contentPostGroup"><br>
      <p>Los post se realizarán cada 3 horas</p><br>
      <p>Los post entre grupos se haran con 3 min diferencia</p><br>
      <!--<input type="text" name="" value="" placeholder="Ingrese Intervalo en horas" id="i_interval"><br>-->
      <button onclick="postGroup()">Post en Grupos</button>
    </div><br>
    <div class="">
      <label width="300">Cerrar Sesión</label><br>
      <button onclick="javascript:logout();">Logout from Facebook</button><br>
    </div>




    <script>
    var jsonGroups = {};
    window.fbAsyncInit = function() {
        FB.init({
        appId: '181795472415931',
        status: true,
        cookie: true,
        xfbml: true
      });
      var status = FB.getLoginStatus();
      };

// Load the SDK asynchronously
(function(d){
var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
if (d.getElementById(id)) {return;}
js = d.createElement('script'); js.id = id; js.async = true;
js.src = "//connect.facebook.net/en_US/all.js";
ref.parentNode.insertBefore(js, ref);
}(document));

function login() {

    FB.login(function(response) {
      FB.api('/me', function(result) {
         $("#contenido").append("<p> Good to see you, "+result.name+"</p>")
       });
    // handle the response
    FB.api('/me/picture','GET',{},function(output) {
        $('#contenido').prepend('<img id="theImg" src="'+output.data.url+'"/>');
      }
    );

    FB.api("/me/groups",function (group_response) {
        if (group_response && !group_response.error) {
          /* handle the result */

          $("#contenido").append("<p>Grupos a los que pertenece: </p><br>");
          var list_groups=group_response.data;
          var li="<ul>";
          for (var i = 0; i < list_groups.length; i++) {
            li+="<li><label><input type='checkbox' name='nameGroup' value='"+list_groups[i].id+"'/>"+list_groups[i].name+"</label></li>";
            jsonGroups[list_groups[i].name]=list_groups[i].id;
          }
          li+="</ul>";
          $("#contenido").append(li);
        }
      }
    );
  }, {scope: 'publish_actions,user_managed_groups'});
}

function logout() {
    FB.logout(function(response) {
      // user is now logged out
    });
}
function post() {
  var texto=$("#i_post").val();
  var timeMinute=$("#i_timePostMine").val();
  timeMinute*=60000; // time in minutes
  FB.login(function(){
    // Note: The call will only work if you accept the permission request
    setTimeout(function() {
      FB.api('/me/feed', 'post', {message: texto},function (response) {
        if (response && !response.error) {
          alert("Post Realizado!");
        }
      });
    }, timeMinute);

  }, {scope: 'publish_actions'});
}
function postGroup() {
  var list_check=[];
  $("input:checkbox[name=nameGroup]:checked").each(function (argument) {
    list_check.push($(this).val());
  });
  var timeHour=$("#i_timePostGroup").val();
  var hour_ms= 3600000; // tiempo de una hora
  // el primer post se hara en 1 hora
  var content=$("#i_contentPostGroup").val();
  // en cada iteracion sumamos 3 min 3*60000= 180000
  for (var j = 0;j < 8; j++) {
    for (var i = 0; i < list_check.length; i++) {
      //en cada loop agregamos 3 min de Intervalo entre grupos
      var route ="/"+list_check[i]+"/feed";
       setTimeout(function() {
        FB.api(
          route,
          "POST",
          {
              "message": content
          },
          function (response) {
            if (response && !response.error) {
              //console.log("Post Realizado!");
            }
          }
      );
      }, hour_ms);
       hour_ms+=180000;//sumamos 3 minutos
    }
    hour_ms+=10800000;//sumamos 3 horas
  }


}

console.log(status);

</script>
  </body>
</html>
