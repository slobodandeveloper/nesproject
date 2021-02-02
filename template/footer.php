<?php
@session_start();
if(!isset($_SESSION['route']))
  die('Direct Access is forbidden');
if(isset($_SESSION['priv']))
  $priv = $_SESSION['priv'];
else
	$priv = 0;
?>
<script>
  var config = {};
    if (localStorage.config) {
        config = JSON.parse(localStorage.config);
        
        if (config.shaderEnabled) toggleShader(true);
        if (config.volume) $('[type=range]').val(Math.min(100, config.volume));
    }
    
    function returnMain() {
        $(".select-game").fadeIn(500);
        $(".emulator").fadeOut(500);
        $("#nav_game").css("display","none");
        $("#nav_main").css("display", "block");
    }
    function save() {
        localStorage.config = JSON.stringify(config);
    }
    
    window.onresize = function() {
        
        if (window.isDebug) return;			
        var isFullscreen = ((screen.availHeight || screen.height-20) <= window.innerHeight);			
        document.body.className = isFullscreen ? 'fullscreen' : '';
        
        if (emu && emu.isPlaying()) emu.render(); // Refresh canvas after resize
        else drawLogo();
    };
    window.onresize();
    
        

    
    
    function drawLogo() {
        return;
        var ctx = $('.nes')[0].getContext('2d');
        ctx.imageSmoothingEnabled = ctx.webkitImageSmoothingEnabled = ctx.mozImageSmoothingEnabled = false;
        ctx.drawImage($('.logo')[0], 0, 0, 256, 240, 0, 0, ctx.canvas.width, ctx.canvas.height);
    }
    
    
    var paused = false;
    function togglePause() {
        if (!emu.isPlaying()) return;
        if (!paused) {
            emu.pause();
            $('.pause-state').prop('src', 'play.svg');
        }
        else {
            emu.resume();
            $('.pause-state').prop('src', 'pause.svg');
        }
        paused = !paused;
    }
    
    var shaderEnabled = false;
    function toggleShader(value) {
        if (value != undefined) config.shaderEnabled = value;
        else config.shaderEnabled = !config.shaderEnabled;
        if (config.shaderEnabled) {
            emu.enableShader('crt.glsl');
        }
        else {
            emu.disableShader();
        }
        $('.button.shader').toggleClass('enabled', config.shaderEnabled);
        save();
    }
</script>
<script id="vertex" type="x-shader/x-vertex">
	attribute vec2 aVertexPosition;
	attribute vec2 aTextureCoord;

	uniform vec2 u_translation;
	uniform vec2 u_resolution;

	varying highp vec2 vTextureCoord;

	void main(void) {
		vec2 cBase = u_resolution / vec2(2, 2);
		gl_Position = vec4(
			((aVertexPosition) + u_translation - cBase) / cBase
		, 0, 1.0) * vec4(1, -1, 1, 1);
		vTextureCoord = aTextureCoord;
	}
</script>
<script id="textureFragment" type="x-shader/x-fragment">
	varying highp vec2 vTextureCoord;
	uniform sampler2D uSampler;
	void main(void) {
		gl_FragColor = texture2D(uSampler, vec2(vTextureCoord.s, vTextureCoord.t));
	}
</script>
<script id="colorFragment" type="x-shader/x-fragment">
	uniform lowp vec4 uColor;
	void main(void) {
		gl_FragColor = uColor;
	}
</script>

<div class="modal fade bd-example-modal-lg" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="exampleModalLabel">Advanced search </h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <div class="s007">
        <form>
            <div class="inner-form">
            <div class="advance-search" id='advance_search_div' style='padding:20px;'>
              <div class="row">
              <div class="input-field">
                <div class="input-select" style='padding-top:3px'>
                  <input type='text' id='search_description' placeholder='Description' class='input_text_field' style='background:#fff'/>
                </div>
              </div>
              <div class="input-field">
                <div class="input-select">
                  <select data-trigger="" id='search_tag' name="choices-single-defaul">
                    <option placeholder="" value="">Tags</option>
                    <?php
                    while($row = $tags->fetch_array(MYSQLI_NUM)) {
                        $id = $row[0];
                        $tagname = $row[1];
                        if($tagname == "")
                          continue;
                        echo "<option value='$id'>$tagname</option>";
                    }
                    ?>
                  </select>
                </div>
              </div>              
            </div>
            <div class="row">
             <div class="input-field">
                <div class="input-select"  style='padding-top:3px'>
                  <input type='text' id='search_credits' placeholder='Credits' class='input_text_field' style='background:#fff'/>
                </div>
              </div>
              <div class="input-field">
                <div class="input-select">
                  <select data-trigger="" id='search_genre' name="choices-single-defaul">
                    <option placeholder="" value="">Genre</option>
                    <?php
                    while($row = $genres->fetch_array(MYSQLI_NUM)) {
                        $id = $row[0];
                        $genrename = $row[1];
                        echo "<option value='$id'>$genrename</option>";
                    }
                    ?>
                  </select>
                </div>
              </div>     
            </div>
            <div class="row second">
             <div class="input-field">
                <div class="input-select"  style='padding-top:3px'>
                  <input type='text' id='search_developer' placeholder='Developer' class='input_text_field' style='background:#fff'/>
                </div>
              </div>
              <div class="input-field">
                <div class="input-select">
                  <select data-trigger="" id='search_sort'>
                    <option placeholder="" value="">Sort by</option>
                    <option value='1'>Alphabetical</option>
                    <option value='2'>Publish date</option>
                    <option value='3'>Rating</option>
                  </select>
                </div>
              </div> 
            </div>
            <div class="row third">            
            <div class="input-field">
                </div>
              <div class="input-field">
                <a class='btn-search' id='btn_searches' style='padding:12px 20px'>Search</a>
                <button class="btn-delete" id="delete">Delete</button>
              </div>
            </div>
            </div></div>
            </form>
            </div>
      </div>
      <div class="modal-footer">
        <div class="maxl" style='padding-right:10px;'>
            <div style='float:left'>                             
            <label class="radio inline" > 
                <input type="radio" id='disp_toggle1' checked>
                <span>Icon mode </span>
            </label>
            </div>
            <div>
            <label class="radio inline"> 
                <input type="radio" id='disp_toggle2'>
                <span>Table mode </span>
            </label>
            </div>
        </div>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="addGenreModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Input genre string </h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <input type="text" class="form-control" name='newgenre' id='newgenre' placeholder="Genre" value="" />
      </div>
      <div class="modal-footer">        
        <button type="button" class="btn btn-primary" data-dismiss="modal" id='save_genre'>Save</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="addTagModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Input tag string </h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <input type="text" class="form-control" name='newtag' id='newtag' placeholder="Tag" value="" />
      </div>
      <div class="modal-footer">        
        <button type="button" class="btn btn-primary" data-dismiss="modal" id='save_tag'>Save</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="GameLinkModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Current game link: </h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <input type="text" class="form-control" id='game_link' value="" />
      </div>
      <div class="modal-footer">        
        <button type="button" class="btn btn-primary" data-dismiss="modal" id='copylink'>Copy Link</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="ReportModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Reason for reporting? </h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <textarea class="form-control" id='txtReport'></textarea>
      </div>
      <div class="modal-footer">        
        <button type="button" class="btn btn-primary" data-dismiss="modal" id='sendreportto'>Send Report</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="QuestionModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">      
      <div class="modal-body">
      Do you want to add this game to your favorite list?
      </div>
      <div class="modal-footer">        
        <button type="button" class="btn btn-primary" data-dismiss="modal" id='addtofavorite'>Add</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="verificateModal" tabindex="-1" role="dialog"aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Check your email and input verification code </h4>
        <button type="button" class="close" data-dismiss="modal" >
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <input type="text" class="form-control" name='verificode' id='verificode' placeholder="Verification Code" value="" />
      </div>
      <div class="modal-footer">
      <button type="button" class="btn btn-secondary" id='Resend_code'>Resend</button>        
        <button type="button" class="btn btn-primary" data-dismiss="modal" id='save_verification'>Submit</button>
        </div>
    </div>
  </div>
</div>
<script type="text/javascript">
	window.isDebug = location.href.match(/debug=1$/i) ? true : false;
	function loadfile(event) {
			var file = event.files[0];
			//file = event;
			if (!file) return;
			if (!file.name.match(/\.nes$/i)) {
				alert('invalid file');
				return;
			}
			var reader = new FileReader();
			reader.onload = function (e) {

				setTimeout(function() { 

					window.emu.volume($('[type=range]').val() / 100)
					if (!window.emu.loadRomData(e.target.result, file.name)) {
						return;
					}
					//$('.button.open').remove();
					window.emu.run($('canvas')[0], window.isDebug);
				});

			};
			reader.readAsArrayBuffer(file);
	}
	function loadfileName(event) {
		fetch(event)
		.then(response => response.blob())
		.then(data => {
			var reader = new FileReader();
			reader.onload = function (e) {

				setTimeout(function() { 

					window.emu.volume($('[type=range]').val() / 100)
					if (!window.emu.loadRomData(e.target.result, event)) {
						return;
					}
					//$('.button.open').remove();
					window.emu.run($('canvas')[0], window.isDebug);
				});

			};
			reader.readAsArrayBuffer(data);
		})
		.catch((error) => {
			console.error('Error:', error);
		});
	}
	function fullscreen() {	
		var canvas = $('canvas')[0];
		if (document.webkitIsFullScreen) return;
		if (!canvas.webkitRequestFullScreen) return;
		canvas.webkitRequestFullScreen();
	};

    const customSelects = document.querySelectorAll("select");
    const deleteBtn = document.getElementById('delete')
    const choices = new Choices('select',
    {
    searchEnabled: false,
    removeItemButton: true,
    itemSelectText: '',
    });
    for (let i = 0; i < customSelects.length; i++)
    {
    customSelects[i].addEventListener('addItem', function(event)
    {
        if (event.detail.value)
        {
        let parent = this.parentNode.parentNode
        parent.classList.add('valid')
        parent.classList.remove('invalid')
        }
        else
        {
        let parent = this.parentNode.parentNode
        parent.classList.add('invalid')
        parent.classList.remove('valid')
        }
    }, false);
    }
    deleteBtn.addEventListener("click", function(e)
    {
    e.preventDefault()
    const deleteAll = document.querySelectorAll('.choices__button')
    for (let i = 0; i < deleteAll.length; i++)
    {
        deleteAll[i].click();
    }
    $(".input_text_field").val("");
    });
    $("#advance_toggle").click(function(e) {
    if($(this).hasClass("fa-chevron-down")) {
        $(this).removeClass("fa-chevron-down");
        $(this).addClass("fa-chevron-up");
        $("#advance_search_div").fadeIn(500);
    }
    else {
        $(this).removeClass("fa-chevron-up");
        $(this).addClass("fa-chevron-down");
        
        $("#advance_search_div").fadeOut(500);
    }
    });    
    $("#basic_search_btn").on("click", function(e) {
        toggle1 = $("#disp_toggle1")[0].checked;
        toggle2 = $("#disp_toggle2")[0].checked;
        
            if(toggle2 == true)
                toggle = 2;
            else
                toggle = 1;
            name = $("#search_name").val();

            $("#hid_name").val(name);
            $("#hid_dsc").val("");
            $("#hid_tag").val("");
            $("#hid_cre").val("");
            $("#hid_gen").val("");
            $("#hid_dev").val("");
            $("#hid_sort").val(0);
            if(toggle == "2") {                
                load_table_page(0);
            }
            else {
                load_icon_page(0);
            }
        
    });
    $("#search_name").on('keydown', function(e) {
        if(e.keyCode == 13) {
            toggle1 = $("#disp_toggle1")[0].checked;
            toggle2 = $("#disp_toggle2")[0].checked;
        
            if(toggle2 == true)
                toggle = 2;
            else
                toggle = 1;
            name = $("#search_name").val();

            $("#hid_name").val(name);
            $("#hid_dsc").val("");
            $("#hid_tag").val("");
            $("#hid_cre").val("");
            $("#hid_gen").val("");
            $("#hid_dev").val("");
            $("#hid_sort").val(0);
            if(toggle == "2") {                
                load_table_page(0);
            }
            else {
                load_icon_page(0);
            }
            e.preventDefault();
        }    
     })
     $("#go_home").on("click", function(e) {
       location.href = "./";
       $(".nav-item").removeClass("active");
       $(this).parents("li").addClass("active");
    });
     function search_detail() {
        name = $("#search_name").val();
        description = $("#search_description").val();
        tag = $("#search_tag").val();
        credit = $("#search_credits").val();
        genre = $("#search_genre").val();
        developer = $("#search_developer").val();
        sort = $("#search_sort").val();
       
        $("#hid_dsc").val(description);
        $("#hid_name").val(name);
        $("#hid_tag").val(tag);
        $("#hid_cre").val(credit);
        $("#hid_gen").val(genre);
        $("#hid_dev").val(developer);
        $("#hid_sort").val(sort);
        
        toggle1 = $("#disp_toggle1")[0].checked;
        toggle2 = $("#disp_toggle2")[0].checked;
        if(toggle2 == true)
            toggle = 2;
        else
            toggle = 1;

        if(toggle == "2") {
            load_table_page(0);
        }
        else {
            
            load_icon_page(0);
        }
     }
    $("#btn_searches").on("click", function() {
        search_detail();
        $("#exampleModal").modal("hide");
    });
    $("#disp_toggle1").on("click",function() {
        $("#disp_toggle2")[0].checked = false;
        load_icon_page(0);
        $("#icon_view_div").fadeIn(500);
        $("#table_view_div").fadeOut(500);
        $(window).trigger('resize');
    })
    $("#disp_toggle2").on("click",function() {
        $("#disp_toggle1")[0].checked = false;
        load_table_page(0);
        $("#icon_view_div").fadeOut(500);
        $("#table_view_div").fadeIn(500);
        $(window).trigger('resize');
    })  
    
    $("#login_pro").on('click', function() {
        $.ajax({url: "./pages/login.php", 
            data : {
            },
            type : "post",
            success: function(result){
                $("#main_body").html(result);	
        }});
       $(".nav-item").removeClass("active");
       $(this).parents("li").addClass("active");     
    })
    $("#login_out").on('click', function() {
        $.ajax({url: "./database.php", 
            data : {
              "do":"login_out"
            },
            type : "post",
            success: function(result){
                location.href = "./";
        }});
       $(".nav-item").removeClass("active");
       $(this).parents("li").addClass("active");     
    })
    $("#signup_pro").on('click', function() {
        $.ajax({url: "./pages/signup.php", 
            data : {
            },
            type : "post",
            success: function(result){
                $("#main_body").html(result);	
        }});
       $(".nav-item").removeClass("active");
       $(this).parents("li").addClass("active");     
    })
    $("#show_all").on('click', function() {
        $.ajax({url: "./pages/all_games.php", 
            data : {
            },
            type : "post",
            success: function(result){
              $("#main_body").html(result);	
              $("#show_all").parent().fadeOut(1);
              $("#show_col").parent().fadeIn(1);              
        }});   
    })
    $("#show_col").on('click', function() {
        $.ajax({url: "./pages/show_collections.php", 
            data : {
            },
            type : "post",
            success: function(result){
              $("#main_body").html(result);	
              $("#show_col").parent().fadeOut(1);
              $("#show_all").parent().fadeIn(1);
        }});   
    })
    $("#copylink").on("click", function() {
      var copyText = document.getElementById("game_link");
      /* Select the text field */
      copyText.select();
      copyText.setSelectionRange(0, 99999); /* For mobile devices */

      /* Copy the text inside the text field */
      document.execCommand("copy");
      toastr['info']("Copied to clipboard.");
    });
    $("#sendreportto").on('click', function() {
        var con = $("#txtReport").val();
        $.ajax({url: "./database.php", 
            data : {
              "do":"send_report",
              "data" : con
            },
            type : "post",
            success: function(result){
              toastr['info']("Report sent.");
        }});   
    })
    $("#addtofavorite").on('click', function() {
        var rid = $("#hid_rid").val();
        $.ajax({url: "./database.php", 
            data : {
              "do":"add_to_favor",
              "rid" : rid
            },
            type : "post",
            success: function(result){
              toastr['info']("Successfully saved. You can check it in your favorite list.");
        }});   
    })
    $("#play_random").on('click', function() {
        $.ajax({url: "./database.php", 
            data : {
              "do":"get_random"
            },
            type : "post",
            success: function(result){
              $.ajax({url: "./gamepage.php", 
                  data : {
                      "rom" : result					
                  },
                  type : "post",
                  success: function(result){
                      $("#main_body").html(result);	
                      let rom_path = $("#hid_path").val();
                      loadfileName(rom_path);				
              }}); 
        }});   
    })
<?php if($priv != 0) :?>
  $("#upload_data").on('click', function() {
    $.ajax({url: "./pages/add.php", 
        data : {
          
        },
        type : "post",
        success: function(result){
          $("#main_body").html(result);	
             
    }});
    $(".nav-item").removeClass("active");
       $(this).parents("li").addClass("active");  
  })
  $("#managegames").on('click', function() {
    $.ajax({url: "./pages/manage_game.php", 
        data : {
          
        },
        type : "post",
        success: function(result){
          $("#main_body").html(result);	
          $(window).trigger('resize');   
    }}); 
    $(".nav-item").removeClass("active");
       $(this).parents("li").addClass("active");      
  })
  $("#manageprofile").on('click', function() {
    $.ajax({url: "./pages/manage_profile.php", 
        data : {
          
        },
        type : "post",
        success: function(result){
          $("#main_body").html(result);  
    }}); 
    $(".nav-item").removeClass("active");
    $(this).parents("li").addClass("active");      
  })
  $("#show_fav").on('click', function() {
    $.ajax({url: "./pages/manage_favs.php", 
        data : {
          
        },
        type : "post",
        success: function(result){
          $("#main_body").html(result);  
    }}); 
    $(".nav-item").removeClass("active");
    $(this).parents("li").addClass("active");      
  })
<?php endif;?>
<?php if($priv==ADMIN) :?>
  $("#managegenres").on('click', function() {
    $.ajax({url: "./pages/manage_genre.php", 
        data : {
          
        },
        type : "post",
        success: function(result){
          $("#main_body").html(result);	
          $(window).trigger('resize');   
    }});   
    $(".nav-item").removeClass("active");
       $(this).parents("li").addClass("active");    
  })
  $("#managetags").on('click', function() {
    $.ajax({url: "./pages/manage_tag.php", 
        data : {
          
        },
        type : "post",
        success: function(result){
          $("#main_body").html(result);	
          $(window).trigger('resize');   
    }});   
    $(".nav-item").removeClass("active");
       $(this).parents("li").addClass("active");    
  })
  $("#manageuser").on('click', function() {
    $.ajax({url: "./pages/manage_user.php", 
        data : {
          
        },
        type : "post",
        success: function(result){
          $("#main_body").html(result);	
          $(window).trigger('resize');   
    }});   
    $(".nav-item").removeClass("active");
       $(this).parents("li").addClass("active");    
  })
<?php endif;?>

  $("#save_verification").click(function(e) {
        var ecode = $("#verificode").val();
        var email = $("#email").val();
        if(email == undefined)  email = "";
        if(ecode == "") {
          toastr['error']('Input verification code.');
          return;
        }
        $.ajax({url: "./database.php", 
            data : {
                "do" : "everification",
                "emailcode" : ecode,
                "email" : email,
            },
            type : "post",
            success: function(result){
              console.log(result);   
               if(result == "0"){
                  toastr['error']("Verification code not matched.");
                  return;
               }    
               else {
                    toastr['success']("Signup success.");
                    $("#verificateModal").modal("hide");                   
                    location.href = "./";
               }
        }});
    });
</script>
</body>
</html>
