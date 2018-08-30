<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->addExternalCss(SITE_TEMPLATE_PATH."/css/sidebar.css");

$GLOBALS['APPLICATION']->SetAdditionalCSS('/bitrix/components/bitrix/rating.vote/templates/like/popup.css');
$uid = $this->randString();
$controller = "BX('blog-".$uid."')";
$arParams["OPTIONS"] = (is_array($arParams["OPTIONS"]) ? $arParams["OPTIONS"] : array());
$arRes = array("data" => array(),
    "page_settings" => array(
      "NavPageCount" => $arResult["NAV_RESULT"]->NavPageCount,
      "NavPageNomer" => $arResult["NAV_RESULT"]->NavPageNomer,
      "NavPageSize" => $arResult["NAV_RESULT"]->NavPageSize,
      "NavRecordCount" => $arResult["NAV_RESULT"]->NavRecordCount,
      "bDescPageNumbering" => $arResult["NAV_RESULT"]->bDescPageNumbering,
      "nPageSize" => $arResult["NAV_RESULT"]->NavPageSize)
  );
foreach($arResult["POST"] as $id => $res)
{
  $res = array(
    "id" => $res["ID"],
    /*"post_text" => TruncateText(($res["MICRO"] != "Y" ? $res["TITLE"]." ".$res["CLEAR_TEXT"] : $res["CLEAR_TEXT"]), $arParams["MESSAGE_LENGTH"]),*/
    "post_text" =>  $res["DETAIL_TEXT"],
    "post_url" => $res["urlToPost"],
    "author_name" => $res["AUTHOR_NAME"],
    "author_avatar_style" => (!empty($res["AUTHOR_AVATAR"]["src"]) ? "url('".$res["AUTHOR_AVATAR"]["src"]."')" : ""),
    "author_avatar" => (!empty($res["AUTHOR_AVATAR"]["src"]) ? "style=\"background:url('".$res["AUTHOR_AVATAR"]["src"]."') no-repeat center; background-size: cover;\"" : ""),
    "author_url" => $res["urlToAuthor"]
  );

  /*if (!trim($res['post_text']))
    $res['post_text'] = getMessage('SBB_READ_EMPTY');*/

  $arRes["data"][] = $res;
}

if ($_REQUEST["AJAX_POST"] == "Y")
{
  $APPLICATION->RestartBuffer();
  echo CUtil::PhpToJSObject($arRes);
  die();
}
CUtil::InitJSCore(array("ajax"));

$arUser = (is_array($arResult["USER"]) ? $arResult["USER"] : array());
$btnTitle = GetMessage("SBB_READ_".$arUser["PERSONAL_GENDER"]);
$btnTitle = (!empty($btnTitle) ? $btnTitle : GetMessage("SBB_READ_"));
$res = reset($arRes["data"]);
?>
<script src="https://code.createjs.com/createjs-2015.11.26.min.js"></script>

<!-- chungnt: tags html -->
<div class="sidebar-widget sidebar-imp-messages" id="blog-<?=$uid?>"<?/*if(empty($arRes["data"])){*/?> style="display: none"<?/*}*/?>>
  <div class="sidebar-imp-mess-top" style="display: none"><?=GetMessage("SBB_IMPORTANT")?></div>
  <div class="sidebar-imp-mess-tmp-wrap" style="display: none">
    <div class="sidebar-imp-mess-tmp" style="display: none">
      <div class="sidebar-imp-mess" id="message-block-<?=$uid?>">
        <div class="sidebar-imp-mess-wrap" id="blog-leaf-<?=$uid?>">

          <div class="user-avatar sidebar-user-avatar user-default-avatar"<?if($res["author_avatar"]!==""){?> <?=$res["author_avatar"]?><?}?>></div>

          <a href="<?=$res["author_url"]?>" class="sidebar-imp-mess-title"><?=$res["author_name"]?></a>
          
          <a href="<?=$res["post_url"]?>" class="sidebar-imp-mess-text"><?=$res["post_text"]?></a>

        </div>
        <div class="sidebar-imp-mess-wrap" id="blog-text-<?=$uid?>">
          <div class="user-avatar sidebar-user-avatar user-default-avatar"<?if($res["author_avatar"]!==""){?> <?=$res["author_avatar"]?><?}?>></div>
          <a href="<?=$res["author_url"]?>" class="sidebar-imp-mess-title"><?=$res["author_name"]?></a>
          <a href="<?=$res["post_url"]?>" class="sidebar-imp-mess-text"><?=$res["post_text"]?></a>
        </div>
        
        <div id="blog-<?=$uid?>-template" class="sidebar-imp-mess-templates" style="display: none;">
          <div class="user-avatar sidebar-user-avatar user-default-avatar" data-bx-author-avatar="true"></div>
          <a href="__author_url__" class="sidebar-imp-mess-title">__author_name__</a>
          <div>__post_text__</div>
        </div>
        <div class="sidebar-imp-mess-bottom">
          <span id="blog-<?=$uid?>-btn" class="sidebar-imp-mess-btn"><?=$btnTitle;?></span>
          <div class="sidebar-imp-mess-nav-block">
            <span class="sidebar-imp-mess-nav-arrow-l" id="blog-<?=$uid?>-right"></span>
            <span class="sidebar-imp-mess-nav-arrow-r" id="blog-<?=$uid?>-left"></span>
            <span id="blog-<?=$uid?>-current" class="sidebar-imp-mess-nav-current-page">1</span><?
              ?><span class="sidebar-imp-mess-nav-separator">/</span><?
            ?><span id="blog-<?=$uid?>-total" class="sidebar-imp-mess-nav-total-page"><?=$arResult["NAV_RESULT"]->NavRecordCount?></span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div id="animation_container" style="position: absolute; top: 0; left: 0; width:1280px; height:720px; z-index: -1; <?if(empty($arRes["data"])){?> display: none<?}?>">
  <canvas id="canvas" width="1280" height="720" style="position: absolute; z-index: -1; <?if(empty($arRes["data"])){?> display: none<?}?>"></canvas>
  
  <canvas id="canvas2" width="1280" height="720" style="position: absolute; z-index: -1; <?if(empty($arRes["data"])){?> display: none<?}?>"></canvas>

  <div id="dom_overlay_container" style="pointer-events:none; overflow:auto; width:1280px; height:720px; position: absolute; left: 0px; top: 0px; display: block;">
  </div>

  <div class="sidebar-imp-mess-wrap" id="announcement" style="overflow:auto; position:absolute; top: 20%; left: 40%; right: 35%; bottom: 30%; z-index: 99999; display: none; height: 300px">
    <div class="user-avatar sidebar-user-avatar user-default-avatar" data-bx-author-avatar="true"></div>
  </div>

  <a href="#" id="agreement" title="Đồng ý" style="position:absolute; top: 9%; left:64%; z-index: 99999; " onclick="fnc_agreement();"></a>
  <a href="#" id="watting" title="Chờ trong 10 phút" style="position:absolute; top: 14%; left:61%; z-index: 99999; " onclick="fnc_watting();"></a>
  <a href="#" class="previous" id="previous" title="Thông báo trước" style="position:absolute; top: 75%; left: 42%; z-index: 99999; color: #c9aa30; font-size: 32px;" onclick="fnc_previous()"></a>
  <div id="page_and_page" style="color: #c9aa30;font-size: 32px;position:absolute;top: 75%; left: 44%; z-index: 99999;"></div>
  <a href="#" class="next" id="next" title="Thông báo tiếp theo" style="position:absolute; top: 75%; left: 48%; z-index: 99999; color: #c9aa30; font-size: 32px;" onclick="fnc_next()"></a>
  <a href="#" id="blog-<?=$uid?>-mark_as_read" title="Đánh dấu đã đọc" style="position:absolute; top: 76%; left:62%; z-index: 99999;" onclick="fnc_mark_as_book()"></a>
</div>

<!-- chungnt: khai bao bien toan cuc -->
<script type="text/javascript">
//khai bao bien thoi gian cac hoat canh
var time = {
  step1_begin: 0,
  step1_delay : 1000,
  step2_begin: 0,
  step2_delay : 2000,
  step3_begin: 0,
  step3_delay : 1900,
  step4_begin: 0,
  step4_delay : 10000,
  step5_begin: 0,
  step5_delay : 5000,
  step6_begin: 0,
  step6_delay : 1500,
  step7_begin: 0,
  step7_delay: 5500,
  step8_begin: 0,
  step8_delay: 3000,
  step9_begin: 0,
  step9_delay: 2000,
  callback_begin: 0,
  callback_delay: 5000
};
</script>

<!-- chungnt: class canvas -->
<script type="text/javascript">

//javascript createjs
(function (cjs, an) {

//============================================================================
//STEP1_BEGIN
//XUẤT HIỆN
var p_step1; // shortcut to reference prototypes
var lib_step1={};var ss={};var img_step1={};
lib_step1.ssMetadata = [];

// symbols:
(lib_step1.happy = function() {
  this.initialize(img_step1.happy);
}).prototype = p_step1 = new cjs.Bitmap();
p_step1.nominalBounds = new cjs.Rectangle(0,0,500,500);// helper functions:

function mc_symbol_clone() {
  var clone = this._cloneProps(new this.constructor(this.mode, this.startPosition, this.loop));
  clone.gotoAndStop(this.currentFrame);
  clone.paused = this.paused;
  clone.framerate = this.framerate;
  return clone;
}

function getMCSymbolPrototype(symbol, nominalBounds, frameBounds) {
  var prototype = cjs.extend(symbol, cjs.MovieClip);
  prototype.clone = mc_symbol_clone;
  prototype.nominalBounds = nominalBounds;
  prototype.frameBounds = frameBounds;
  return prototype;
  }

(lib_step1.Symbol4 = function(mode,startPosition,loop=false) {
  this.initialize(mode,startPosition,loop,{});

  // Layer 1
  this.instance = new lib_step1.happy();
  this.instance.parent = this;
  this.instance.setTransform(0,0,0.434,0.434);
  this.timeline.addTween(cjs.Tween.get(this.instance).wait(1));
}).prototype = getMCSymbolPrototype(lib_step1.Symbol4, new cjs.Rectangle(0,0,129,131.2), null);

// stage content:
(lib_step1.notice1_cảnh1_xuấthiện = function(mode,startPosition,loop) {
  this.initialize(mode,startPosition,false,{});

  // happy_first
  this.instance = new lib_step1.Symbol4();
  this.instance.parent = this;
  this.instance.setTransform(1156.5,-45.3,0.379,0.379,0,0,0,64.3,50);

  this.timeline.addTween(cjs.Tween.get(this.instance).wait(1)
    .to({regX:64.5,regY:65.6,scaleX:0.39,scaleY:0.39,x:1152.2,y:-31.3},0)
    .wait(1).to({scaleX:0.4,scaleY:0.4,x:1147.6,y:-23.2},0)
    .wait(1).to({scaleX:0.41,scaleY:0.41,x:1142.9,y:-15.2},0)
    .wait(1).to({scaleX:0.42,scaleY:0.42,x:1138.1,y:-7.3},0)
    .wait(1).to({scaleX:0.42,scaleY:0.42,x:1133.1,y:0.4},0)
    .wait(1).to({scaleX:0.43,scaleY:0.43,x:1127.9,y:8.1},0)
    .wait(1).to({scaleX:0.44,scaleY:0.44,x:1122.6,y:15.7},0)
    .wait(1).to({scaleX:0.45,scaleY:0.45,x:1117.1,y:23.1},0)
    .wait(1).to({scaleX:0.46,scaleY:0.46,x:1111.4,y:30.3},0)
    .wait(1).to({scaleX:0.47,scaleY:0.47,x:1106,y:36.9},0)
    .wait(1).to({scaleX:0.47,scaleY:0.47,x:1100.5,y:43.2},0)
    .wait(1).to({scaleX:0.48,scaleY:0.48,x:1094.8,y:49.4},0)
    .wait(1).to({scaleX:0.49,scaleY:0.49,x:1089.1,y:55.5},0)
    .wait(1).to({scaleX:0.49,scaleY:0.49,x:1083.1,y:61.5},0)
    .wait(1).to({scaleX:0.5,scaleY:0.5,x:1077,y:67.3},0)
    .wait(1).to({scaleX:0.5,scaleY:0.5,x:1071,y:72.6},0)
    .wait(1).to({scaleX:0.51,scaleY:0.51,x:1065,y:77.7},0)
    .wait(1).to({scaleX:0.52,scaleY:0.52,x:1058.8,y:82.7},0)
    .wait(1).to({scaleX:0.52,scaleY:0.52,x:1052.5,y:87.5},0)
    .wait(1).to({scaleX:0.53,scaleY:0.53,x:1046,y:92.2},0)
    .wait(1).to({scaleX:0.54,scaleY:0.54,x:1039.4,y:96.6},0)
    .wait(1).to({scaleX:0.55,scaleY:0.55,x:1032.7,y:100.9},0)
    .wait(1).to({scaleX:0.55,scaleY:0.55,x:1025.9,y:104.9},0)
    .wait(1).to({scaleX:0.56,scaleY:0.56,x:1019,y:108.8},0)
    .wait(1).to({scaleX:0.56,scaleY:0.56,x:1011.9,y:112.4},0)
    .wait(1).to({scaleX:0.57,scaleY:0.57,x:1004.7,y:115.9},0)
    .wait(1).to({scaleX:0.58,scaleY:0.58,x:997.4,y:119.2},0)
    .wait(1).to({scaleX:0.59,scaleY:0.59,x:990,y:122.3},0)
    .wait(1).to({scaleX:0.6,scaleY:0.6,x:982.5,y:125.1},0)
    .wait(1).to({scaleX:0.61,scaleY:0.61,x:975,y:127.7},0)
    .wait(1).to({scaleX:0.62,scaleY:0.62,x:967.4,y:130.1},0)
    .wait(1).to({scaleX:0.63,scaleY:0.63,x:959.6,y:132.2},0)
    .wait(1).to({scaleX:0.64,scaleY:0.64,x:951.9,y:134.2},0)
    .wait(1).to({scaleX:0.65,scaleY:0.65,x:944.1,y:135.9},0)
    .wait(1).to({scaleX:0.65,scaleY:0.65,x:936.3,y:137.4},0)
    .wait(1).to({scaleX:0.66,scaleY:0.66,x:928.4,y:138.7},0)
    .wait(1).to({scaleX:0.67,scaleY:0.67,x:920.5,y:139.9},0)
    .wait(1).to({scaleX:0.68,scaleY:0.68,x:912.6,y:140.7},0)
    .wait(1).to({scaleX:0.69,scaleY:0.69,x:904.7,y:141.4},0)
    .wait(1));

}).prototype = p_step1 = new cjs.MovieClip();
p_step1.nominalBounds = new cjs.Rectangle(1772.1,295.7,100,100);

// library properties:
lib_step1.properties = {
  id: '9D33D0B2ABC183428F26CA73B2961738_1',
  width: 1280,
  height: 720,
  fps: 24,
  color: "#FFFFFF",
  opacity: 1.00,
  manifest: [
    {src:"/bitrix/components/vportal/vivi_notify/templates/.default/images/happy.png?1533277727366", id:"happy"}
  ],
  preloads: []
};

// bootstrap callback support:

(lib_step1.Stage = function(canvas) {
  createjs.Stage.call(this, canvas);
}).prototype = p_step1 = new createjs.Stage();

p_step1.setAutoPlay = function(autoPlay) {
  this.tickEnabled = autoPlay;
}
p_step1.play = function() { this.tickEnabled = true; this.getChildAt(0).gotoAndPlay(this.getTimelinePosition()) }
p_step1.stop = function(ms) { if(ms) this.seek(ms); this.tickEnabled = false; }
p_step1.seek = function(ms) { this.tickEnabled = true; this.getChildAt(0).gotoAndStop(lib_step1.properties.fps * ms / 1000); }
p_step1.getDuration = function() { return this.getChildAt(0).totalFrames / lib_step1.properties.fps * 1000; }

p_step1.getTimelinePosition = function() { return this.getChildAt(0).currentFrame / lib_step1.properties.fps * 1000; }

//STEP_END 1
//===============================================================================
//STEP2_BEGIN
var p_step2; // shortcut to reference prototypes
var lib_step2={};var ss_step2={};var img_step2={};
lib_step2.ssMetadata = [
  {name:"notice1_cảnh2_hạ cánh_atlas_", frames: [[0,0,297,302],[379,114,78,48],[299,75,78,48],[459,114,39,83],[299,125,39,83],[299,0,58,73],[359,0,58,73],[419,0,76,55],[419,57,76,55],[299,210,85,35],[386,238,85,35],[425,199,83,37],[340,164,83,37],[157,304,62,28],[93,304,62,28],[65,304,26,67],[299,298,26,67],[405,275,37,61],[473,238,37,61],[299,247,51,49],[352,275,51,49],[0,304,63,32],[444,301,63,32],[327,326,65,24],[221,304,65,24],[394,338,41,14],[327,352,41,14],[141,334,17,44],[160,334,17,44],[93,334,22,44],[117,334,22,44],[221,330,30,39],[253,330,30,39],[379,75,37,30],[340,125,37,30],[0,338,42,17],[444,335,42,17]]}
];

// symbols:
(lib_step2.bodyw2 = function() {
  this.spriteSheet = ss_step2["notice1_cảnh2_hạ cánh_atlas_"];
  this.gotoAndStop(0);
}).prototype = p_step2 = new cjs.Sprite();

(lib_step2.c1 = function() {
  this.spriteSheet = ss_step2["notice1_cảnh2_hạ cánh_atlas_"];
  this.gotoAndStop(1);
}).prototype = p_step2 = new cjs.Sprite();

(lib_step2.c1_1 = function() {
  this.spriteSheet = ss_step2["notice1_cảnh2_hạ cánh_atlas_"];
  this.gotoAndStop(2);
}).prototype = p_step2 = new cjs.Sprite();

(lib_step2.c1_2 = function() {
  this.spriteSheet = ss_step2["notice1_cảnh2_hạ cánh_atlas_"];
  this.gotoAndStop(3);
}).prototype = p_step2 = new cjs.Sprite();

(lib_step2.c1_3 = function() {
  this.spriteSheet = ss_step2["notice1_cảnh2_hạ cánh_atlas_"];
  this.gotoAndStop(4);
}).prototype = p_step2 = new cjs.Sprite();

(lib_step2.c1_4 = function() {
  this.spriteSheet = ss_step2["notice1_cảnh2_hạ cánh_atlas_"];
  this.gotoAndStop(5);
}).prototype = p_step2 = new cjs.Sprite();

(lib_step2.c1_5 = function() {
  this.spriteSheet = ss_step2["notice1_cảnh2_hạ cánh_atlas_"];
  this.gotoAndStop(6);
}).prototype = p_step2 = new cjs.Sprite();

(lib_step2.c1_6 = function() {
  this.spriteSheet = ss_step2["notice1_cảnh2_hạ cánh_atlas_"];
  this.gotoAndStop(7);
}).prototype = p_step2 = new cjs.Sprite();

(lib_step2.c1_7 = function() {
  this.spriteSheet = ss_step2["notice1_cảnh2_hạ cánh_atlas_"];
  this.gotoAndStop(8);
}).prototype = p_step2 = new cjs.Sprite();

(lib_step2.c1_8 = function() {
  this.spriteSheet = ss_step2["notice1_cảnh2_hạ cánh_atlas_"];
  this.gotoAndStop(9);
}).prototype = p_step2 = new cjs.Sprite();

(lib_step2.c1_9 = function() {
  this.spriteSheet = ss_step2["notice1_cảnh2_hạ cánh_atlas_"];
  this.gotoAndStop(10);
}).prototype = p_step2 = new cjs.Sprite();

(lib_step2.c1_10 = function() {
  this.spriteSheet = ss_step2["notice1_cảnh2_hạ cánh_atlas_"];
  this.gotoAndStop(11);
}).prototype = p_step2 = new cjs.Sprite();

(lib_step2.c1_11 = function() {
  this.spriteSheet = ss_step2["notice1_cảnh2_hạ cánh_atlas_"];
  this.gotoAndStop(12);
}).prototype = p_step2 = new cjs.Sprite();

(lib_step2.c2 = function() {
  this.spriteSheet = ss_step2["notice1_cảnh2_hạ cánh_atlas_"];
  this.gotoAndStop(13);
}).prototype = p_step2 = new cjs.Sprite();

(lib_step2.c2_1 = function() {
  this.spriteSheet = ss_step2["notice1_cảnh2_hạ cánh_atlas_"];
  this.gotoAndStop(14);
}).prototype = p_step2 = new cjs.Sprite();

(lib_step2.c2_2 = function() {
  this.spriteSheet = ss_step2["notice1_cảnh2_hạ cánh_atlas_"];
  this.gotoAndStop(15);
}).prototype = p_step2 = new cjs.Sprite();

(lib_step2.c2_3 = function() {
  this.spriteSheet = ss_step2["notice1_cảnh2_hạ cánh_atlas_"];
  this.gotoAndStop(16);
}).prototype = p_step2 = new cjs.Sprite();

(lib_step2.c2_4 = function() {
  this.spriteSheet = ss_step2["notice1_cảnh2_hạ cánh_atlas_"];
  this.gotoAndStop(17);
}).prototype = p_step2 = new cjs.Sprite();

(lib_step2.c2_5 = function() {
  this.spriteSheet = ss_step2["notice1_cảnh2_hạ cánh_atlas_"];
  this.gotoAndStop(18);
}).prototype = p_step2 = new cjs.Sprite();

(lib_step2.c2_6 = function() {
  this.spriteSheet = ss_step2["notice1_cảnh2_hạ cánh_atlas_"];
  this.gotoAndStop(19);
}).prototype = p_step2 = new cjs.Sprite();

(lib_step2.c2_7 = function() {
  this.spriteSheet = ss_step2["notice1_cảnh2_hạ cánh_atlas_"];
  this.gotoAndStop(20);
}).prototype = p_step2 = new cjs.Sprite();

(lib_step2.c2_8 = function() {
  this.spriteSheet = ss_step2["notice1_cảnh2_hạ cánh_atlas_"];
  this.gotoAndStop(21);
}).prototype = p_step2 = new cjs.Sprite();

(lib_step2.c2_9 = function() {
  this.spriteSheet = ss_step2["notice1_cảnh2_hạ cánh_atlas_"];
  this.gotoAndStop(22);
}).prototype = p_step2 = new cjs.Sprite();

(lib_step2.c2_10 = function() {
  this.spriteSheet = ss_step2["notice1_cảnh2_hạ cánh_atlas_"];
  this.gotoAndStop(23);
}).prototype = p_step2 = new cjs.Sprite();

(lib_step2.c2_11 = function() {
  this.spriteSheet = ss_step2["notice1_cảnh2_hạ cánh_atlas_"];
  this.gotoAndStop(24);
}).prototype = p_step2 = new cjs.Sprite();

(lib_step2.c3 = function() {
  this.spriteSheet = ss_step2["notice1_cảnh2_hạ cánh_atlas_"];
  this.gotoAndStop(25);
}).prototype = p_step2 = new cjs.Sprite();

(lib_step2.c3_1 = function() {
  this.spriteSheet = ss_step2["notice1_cảnh2_hạ cánh_atlas_"];
  this.gotoAndStop(26);
}).prototype = p_step2 = new cjs.Sprite();

(lib_step2.c3_2 = function() {
  this.spriteSheet = ss_step2["notice1_cảnh2_hạ cánh_atlas_"];
  this.gotoAndStop(27);
}).prototype = p_step2 = new cjs.Sprite();

(lib_step2.c3_3 = function() {
  this.spriteSheet = ss_step2["notice1_cảnh2_hạ cánh_atlas_"];
  this.gotoAndStop(28);
}).prototype = p_step2 = new cjs.Sprite();

(lib_step2.c3_4 = function() {
  this.spriteSheet = ss_step2["notice1_cảnh2_hạ cánh_atlas_"];
  this.gotoAndStop(29);
}).prototype = p_step2 = new cjs.Sprite();

(lib_step2.c3_5 = function() {
  this.spriteSheet = ss_step2["notice1_cảnh2_hạ cánh_atlas_"];
  this.gotoAndStop(30);
}).prototype = p_step2 = new cjs.Sprite();

(lib_step2.c3_6 = function() {
  this.spriteSheet = ss_step2["notice1_cảnh2_hạ cánh_atlas_"];
  this.gotoAndStop(31);
}).prototype = p_step2 = new cjs.Sprite();

(lib_step2.c3_7 = function() {
  this.spriteSheet = ss_step2["notice1_cảnh2_hạ cánh_atlas_"];
  this.gotoAndStop(32);
}).prototype = p_step2 = new cjs.Sprite();

(lib_step2.c3_8 = function() {
  this.spriteSheet = ss_step2["notice1_cảnh2_hạ cánh_atlas_"];
  this.gotoAndStop(33);
}).prototype = p_step2 = new cjs.Sprite();

(lib_step2.c3_9 = function() {
  this.spriteSheet = ss_step2["notice1_cảnh2_hạ cánh_atlas_"];
  this.gotoAndStop(34);
}).prototype = p_step2 = new cjs.Sprite();

(lib_step2.c3_10 = function() {
  this.spriteSheet = ss_step2["notice1_cảnh2_hạ cánh_atlas_"];
  this.gotoAndStop(35);
}).prototype = p_step2 = new cjs.Sprite();

(lib_step2.c3_11 = function() {
  this.spriteSheet = ss_step2["notice1_cảnh2_hạ cánh_atlas_"];
  this.gotoAndStop(36);
}).prototype = p_step2 = new cjs.Sprite();

// stage content:
(lib_step2.notice1_cảnh2_hạcánh = function(mode,startPosition,loop) {
  this.initialize(mode,startPosition,false,{});

  // c1
  this.instance = new lib_step2.c1_2();
  this.instance.parent = this;
  this.instance.setTransform(1009,150,0.402,0.402);
  this.instance._off = true;

  this.timeline.addTween(cjs.Tween.get(this.instance).wait(51).to({_off:false},0).to({_off:true},11).wait(20));

  // c2
  this.instance_1 = new lib_step2.c2_2();
  this.instance_1.parent = this;
  this.instance_1.setTransform(1006,150,0.402,0.402);
  this.instance_1._off = true;

  this.timeline.addTween(cjs.Tween.get(this.instance_1).wait(51).to({_off:false},0).to({_off:true},11).wait(20));

  // c3
  this.instance_2 = new lib_step2.c3_2();
  this.instance_2.parent = this;
  this.instance_2.setTransform(1004,153,0.402,0.402);
  this.instance_2._off = true;

  this.timeline.addTween(cjs.Tween.get(this.instance_2).wait(51).to({_off:false},0).to({_off:true},11).wait(20));

  // c1
  this.instance_3 = new lib_step2.c1_3();
  this.instance_3.parent = this;
  this.instance_3.setTransform(967,150,0.402,0.402);
  this.instance_3._off = true;

  this.timeline.addTween(cjs.Tween.get(this.instance_3).wait(51).to({_off:false},0).to({_off:true},11).wait(20));

  // c2
  this.instance_4 = new lib_step2.c2_3();
  this.instance_4.parent = this;
  this.instance_4.setTransform(975,150,0.402,0.402);
  this.instance_4._off = true;

  this.timeline.addTween(cjs.Tween.get(this.instance_4).wait(51).to({_off:false},0).to({_off:true},11).wait(20));

  // c3
  this.instance_5 = new lib_step2.c3_3();
  this.instance_5.parent = this;
  this.instance_5.setTransform(980,153,0.402,0.402);
  this.instance_5._off = true;

  this.timeline.addTween(cjs.Tween.get(this.instance_5).wait(51).to({_off:false},0).to({_off:true},11).wait(20));

  // c1
  this.instance_6 = new lib_step2.c1_4();
  this.instance_6.parent = this;
  this.instance_6.setTransform(1012,149,0.418,0.418);
  this.instance_6._off = true;

  this.timeline.addTween(cjs.Tween.get(this.instance_6).wait(40).to({_off:false},0).to({_off:true},11).wait(31));

  // c2
  this.instance_7 = new lib_step2.c2_4();
  this.instance_7.parent = this;
  this.instance_7.setTransform(1011,150,0.418,0.418);
  this.instance_7._off = true;

  this.timeline.addTween(cjs.Tween.get(this.instance_7).wait(40).to({_off:false},0).to({_off:true},11).wait(31));

  // c3
  this.instance_8 = new lib_step2.c3_4();
  this.instance_8.parent = this;
  this.instance_8.setTransform(1010,154,0.418,0.418);
  this.instance_8._off = true;

  this.timeline.addTween(cjs.Tween.get(this.instance_8).wait(40).to({_off:false},0).to({_off:true},11).wait(31));

  // c1
  this.instance_9 = new lib_step2.c1_5();
  this.instance_9.parent = this;
  this.instance_9.setTransform(958,149,0.418,0.418);
  this.instance_9._off = true;

  this.timeline.addTween(cjs.Tween.get(this.instance_9).wait(40).to({_off:false},0).to({_off:true},11).wait(31));

  // c2
  this.instance_10 = new lib_step2.c2_5();
  this.instance_10.parent = this;
  this.instance_10.setTransform(969,150,0.418,0.418);
  this.instance_10._off = true;

  this.timeline.addTween(cjs.Tween.get(this.instance_10).wait(40).to({_off:false},0).to({_off:true},11).wait(31));

  // c3
  this.instance_11 = new lib_step2.c3_5();
  this.instance_11.parent = this;
  this.instance_11.setTransform(975,154,0.418,0.418);
  this.instance_11._off = true;

  this.timeline.addTween(cjs.Tween.get(this.instance_11).wait(40).to({_off:false},0).to({_off:true},11).wait(31));

  // c1
  this.instance_12 = new lib_step2.c1_6();
  this.instance_12.parent = this;
  this.instance_12.setTransform(1009,150,0.411,0.411);
  this.instance_12._off = true;

  this.timeline.addTween(cjs.Tween.get(this.instance_12).wait(31).to({_off:false},0).to({_off:true},9).wait(42));

  // c2
  this.instance_13 = new lib_step2.c2_6();
  this.instance_13.parent = this;
  this.instance_13.setTransform(1008,152,0.411,0.411);
  this.instance_13._off = true;

  this.timeline.addTween(cjs.Tween.get(this.instance_13).wait(31).to({_off:false},0).to({_off:true},9).wait(42));

  // c3
  this.instance_14 = new lib_step2.c3_6();
  this.instance_14.parent = this;
  this.instance_14.setTransform(1008,156,0.411,0.411);
  this.instance_14._off = true;

  this.timeline.addTween(cjs.Tween.get(this.instance_14).wait(31).to({_off:false},0).to({_off:true},9).wait(42));

  // c1
  this.instance_15 = new lib_step2.c1_7();
  this.instance_15.parent = this;
  this.instance_15.setTransform(951,150,0.411,0.411);
  this.instance_15._off = true;

  this.timeline.addTween(cjs.Tween.get(this.instance_15).wait(31).to({_off:false},0).to({_off:true},9).wait(42));

  // c2
  this.instance_16 = new lib_step2.c2_7();
  this.instance_16.parent = this;
  this.instance_16.setTransform(962,152,0.411,0.411);
  this.instance_16._off = true;

  this.timeline.addTween(cjs.Tween.get(this.instance_16).wait(31).to({_off:false},0).to({_off:true},9).wait(42));

  // c3
  this.instance_17 = new lib_step2.c3_7();
  this.instance_17.parent = this;
  this.instance_17.setTransform(970,156,0.411,0.411);
  this.instance_17._off = true;

  this.timeline.addTween(cjs.Tween.get(this.instance_17).wait(31).to({_off:false},0).to({_off:true},9).wait(42));

  // c1
  this.instance_18 = new lib_step2.c1_8();
  this.instance_18.parent = this;
  this.instance_18.setTransform(1010,146,0.415,0.415);
  this.instance_18._off = true;

  this.timeline.addTween(cjs.Tween.get(this.instance_18).wait(20).to({_off:false},0).to({_off:true},11).wait(51));

  // c2
  this.instance_19 = new lib_step2.c2_8();
  this.instance_19.parent = this;
  this.instance_19.setTransform(1010,150,0.415,0.415);
  this.instance_19._off = true;

  this.timeline.addTween(cjs.Tween.get(this.instance_19).wait(20).to({_off:false},0).to({_off:true},11).wait(51));

  // c3
  this.instance_20 = new lib_step2.c3_8();
  this.instance_20.parent = this;
  this.instance_20.setTransform(1012,152,0.415,0.415);
  this.instance_20._off = true;

  this.timeline.addTween(cjs.Tween.get(this.instance_20).wait(20).to({_off:false},0).to({_off:true},11).wait(51));

  // c1
  this.instance_21 = new lib_step2.c1_9();
  this.instance_21.parent = this;
  this.instance_21.setTransform(948,146,0.415,0.415);
  this.instance_21._off = true;

  this.timeline.addTween(cjs.Tween.get(this.instance_21).wait(20).to({_off:false},0).to({_off:true},11).wait(51));

  // c2
  this.instance_22 = new lib_step2.c2_9();
  this.instance_22.parent = this;
  this.instance_22.setTransform(957,150,0.415,0.415);
  this.instance_22._off = true;

  this.timeline.addTween(cjs.Tween.get(this.instance_22).wait(20).to({_off:false},0).to({_off:true},11).wait(51));

  // c3
  this.instance_23 = new lib_step2.c3_9();
  this.instance_23.parent = this;
  this.instance_23.setTransform(967,152,0.415,0.415);
  this.instance_23._off = true;

  this.timeline.addTween(cjs.Tween.get(this.instance_23).wait(20).to({_off:false},0).to({_off:true},11).wait(51));

  // c1
  this.instance_24 = new lib_step2.c1_10();
  this.instance_24.parent = this;
  this.instance_24.setTransform(1008,135,0.415,0.415);
  this.instance_24._off = true;

  this.timeline.addTween(cjs.Tween.get(this.instance_24).wait(10).to({_off:false},0).to({_off:true},10).wait(62));

  // c2
  this.instance_25 = new lib_step2.c2_10();
  this.instance_25.parent = this;
  this.instance_25.setTransform(1009,145,0.415,0.415);
  this.instance_25._off = true;

  this.timeline.addTween(cjs.Tween.get(this.instance_25).wait(10).to({_off:false},0).to({_off:true},10).wait(62));

  // c3
  this.instance_26 = new lib_step2.c3_10();
  this.instance_26.parent = this;
  this.instance_26.setTransform(1010,152,0.415,0.415);
  this.instance_26._off = true;

  this.timeline.addTween(cjs.Tween.get(this.instance_26).wait(10).to({_off:false},0).to({_off:true},10).wait(62));

  // c1
  this.instance_27 = new lib_step2.c1_11();
  this.instance_27.parent = this;
  this.instance_27.setTransform(949,135,0.415,0.415);
  this.instance_27._off = true;

  this.timeline.addTween(cjs.Tween.get(this.instance_27).wait(10).to({_off:false},0).to({_off:true},10).wait(62));

  // c2
  this.instance_28 = new lib_step2.c2_11();
  this.instance_28.parent = this;
  this.instance_28.setTransform(955,145,0.415,0.415);
  this.instance_28._off = true;

  this.timeline.addTween(cjs.Tween.get(this.instance_28).wait(10).to({_off:false},0).to({_off:true},10).wait(62));

  // c3
  this.instance_29 = new lib_step2.c3_11();
  this.instance_29.parent = this;
  this.instance_29.setTransform(964,152,0.415,0.415);
  this.instance_29._off = true;

  this.timeline.addTween(cjs.Tween.get(this.instance_29).wait(10).to({_off:false},0).to({_off:true},10).wait(62));

  // c1
  this.instance_30 = new lib_step2.c1();
  this.instance_30.parent = this;
  this.instance_30.setTransform(1009,124,0.419,0.419);

  this.timeline.addTween(cjs.Tween.get(this.instance_30).to({_off:true},10).wait(72));

  // c2
  this.instance_31 = new lib_step2.c2();
  this.instance_31.parent = this;
  this.instance_31.setTransform(1012,136,0.419,0.419);

  this.timeline.addTween(cjs.Tween.get(this.instance_31).to({_off:true},10).wait(72));

  // c3
  this.instance_32 = new lib_step2.c3();
  this.instance_32.parent = this;
  this.instance_32.setTransform(1013,144,0.419,0.419);

  this.timeline.addTween(cjs.Tween.get(this.instance_32).to({_off:true},10).wait(72));

  // c1
  this.instance_33 = new lib_step2.c1_1();
  this.instance_33.parent = this;
  this.instance_33.setTransform(953,124,0.419,0.419);

  this.timeline.addTween(cjs.Tween.get(this.instance_33).to({_off:true},10).wait(72));

  // c2
  this.instance_34 = new lib_step2.c2_1();
  this.instance_34.parent = this;
  this.instance_34.setTransform(958,136,0.419,0.419);

  this.timeline.addTween(cjs.Tween.get(this.instance_34).to({_off:true},10).wait(72));

  // c3
  this.instance_35 = new lib_step2.c3_1();
  this.instance_35.parent = this;
  this.instance_35.setTransform(965,144,0.419,0.419);

  this.timeline.addTween(cjs.Tween.get(this.instance_35).to({_off:true},10).wait(72));

  // body w2
  this.instance_36 = new lib_step2.bodyw2();
  this.instance_36.parent = this;
  this.instance_36.setTransform(940,50,0.407,0.407);

  this.timeline.addTween(cjs.Tween.get(this.instance_36).wait(10).to({y:58},0).wait(10).to({y:62},0).wait(11).to({y:67},0).wait(9).to({y:66},0).wait(11).to({_off:true},11).wait(20));

}).prototype = p_step2 = new cjs.MovieClip();
p_step2.nominalBounds = new cjs.Rectangle(1580,410,121,123.1);
// library properties:
lib_step2.properties = {
  id: '9D33D0B2ABC183428F26CA73B2961738_2',
  width: 1280,
  height: 720,
  fps: 24,
  color: "#FFFFFF",
  opacity: 1.00,
  manifest: [
    {src:"/bitrix/components/vportal/vivi_notify/templates/.default/images/notice1_cảnh2_hạ cánh_atlas_.png?1533277753571", id:"notice1_cảnh2_hạ cánh_atlas_"}
  ],
  preloads: []
};

// bootstrap callback support:
(lib_step2.Stage = function(canvas) {
  createjs.Stage.call(this, canvas);
}).prototype = p_step2 = new createjs.Stage();

p_step2.setAutoPlay = function(autoPlay) {
  this.tickEnabled = autoPlay;
}
p_step2.play = function() { this.tickEnabled = true; this.getChildAt(0).gotoAndPlay(this.getTimelinePosition()) }
p_step2.stop = function(ms) { if(ms) this.seek(ms); this.tickEnabled = false; }
p_step2.seek = function(ms) { this.tickEnabled = true; this.getChildAt(0).gotoAndStop(lib_step2.properties.fps * ms / 1000); }
p_step2.getDuration = function() { return this.getChildAt(0).totalFrames / lib_step2.properties.fps * 1000; }

p_step2.getTimelinePosition = function() { return this.getChildAt(0).currentFrame / lib_step2.properties.fps * 1000; }
//STEP2_END
//===============================================================================

//STEP3_BEGIN
var p_step3; // shortcut to reference prototypes
var lib_step3={};var ss_step3={};var img_step3={};
lib_step3.ssMetadata = [];

(lib_step3.cute = function() {
  this.initialize(img_step3.cute);
}).prototype = p_step3 = new cjs.Bitmap();
p_step3.nominalBounds = new cjs.Rectangle(0,0,303,281);

// stage content:
(lib_step3.notice1_cảnh3_chàohỏi = function(mode,startPosition,loop) {
  this.initialize(mode,startPosition,false,{});

  // cute
  this.instance = new lib_step3.cute();
  this.instance.parent = this;
  this.instance.setTransform(942,77,0.4,0.4);
  this.instance._off = false;

  this.timeline.addTween(cjs.Tween.get(this.instance).wait(1).to({_off:false},0).to({_off:false},28).wait(74));

  // text
  this.shape = new cjs.Shape();
  this.shape.graphics.f("#000000").s().p("AgGA3IAAgQIAOAAIAAAQgAgGAcIAAgEQAAgIACgFIAFgJIAJgIIAJgJQACgFAAgDQAAgIgGgGQgGgGgJAAQgHAAgGAFQgFAGgCALIgOgCQACgOAJgJQAJgHAOAAQAQgBAJAJQAKAJAAAMQAAAGgEAGQgDAHgKAHQgGAGgCADIgDAFIgBAMg");
  this.shape.setTransform(945.4,136);

  this.shape_1 = new cjs.Shape();
  this.shape_1.graphics.f("#000000").s().p("AgHA0IAAgPIAOAAIAAAPgAgcAXQgHgGAAgKQAAgFADgEQACgFAFgDQAEgDAFgBIAMgCQAPgCAHgDIAAgDQAAgIgDgDQgFgFgKAAQgJAAgEADQgFAEgCAIIgNgCQACgIAEgFQAEgFAIgDQAHgDAKAAQAJAAAHADQAGACADADQADAEABAFIABANIAAARQAAASABAEQAAAFADAFIgOAAQgCgEgBgGQgHAGgHADQgFADgIAAQgNAAgHgHgAgCgFIgMACQgDACgCACQgCACAAAEQAAAFAEAEQAEADAIAAQAHAAAFgDQAGgDADgGQACgFAAgHIAAgFQgHADgNACg");
  this.shape_1.setTransform(937,138.7);

  this.shape_2 = new cjs.Shape();
  this.shape_2.graphics.f("#000000").s().p("AgXAxQgIgGAAgMIANABQABAGAEADQAEADAJAAQAIAAAFgDQAFgEACgGQABgFAAgMQgJAKgMAAQgQAAgJgLQgIgMAAgPQAAgLAEgKQAEgKAHgEQAIgFAKgBQANAAAJAMIAAgKIAMAAIAABCQAAATgEAHQgDAIgIAEQgIAFgLAAQgOgBgJgGgAgOglQgGAIAAAOQAAAPAGAGQAGAHAIAAQAKAAAGgHQAGgGAAgPQAAgOgGgIQgHgGgJAAQgIAAgGAGg");
  this.shape_2.setTransform(924.3,139.1);

  this.shape_3 = new cjs.Shape();
  this.shape_3.graphics.f("#000000").s().p("AATAoIAAgvQAAgIgCgEQgCgEgDgCQgFgCgFAAQgHAAgHAFQgFAFgBAPIAAAqIgNAAIAAhNIAMAAIAAALQAJgNAPAAQAHAAAGADQAGACADAEQACAEACAGIAAANIAAAvg");
  this.shape_3.setTransform(916.2,137.5);

  this.shape_4 = new cjs.Shape();
  this.shape_4.graphics.f("#000000").s().p("AgZAsQgKgKAAgUQAAgUALgLQALgJANAAQAQAAAKALQALALAAARQgBAPgEAIQgFAJgIAFQgJAFgKAAQgQAAgJgLgAgPgHQgHAHAAAOQAAAPAHAHQAGAIAJAAQAKAAAHgIQAGgHAAgPQAAgOgGgHQgIgIgJABQgJgBgGAIgAAIgiIgIgMIgHAMIgQAAIARgUIAMAAIARAUg");
  this.shape_4.setTransform(907.9,136.2);

  this.shape_5 = new cjs.Shape();
  this.shape_5.graphics.f("#000000").s().p("AASA2IAAgyQAAgIgDgFQgFgEgIAAQgFAAgFADQgFADgCAFQgDAEABAJIAAArIgOAAIAAhrIAOAAIAAAnQAJgLANAAQAJAAAGAEQAGADADAGQACAGAAAKIAAAyg");
  this.shape_5.setTransform(899.5,136.1);

  this.shape_6 = new cjs.Shape();
  this.shape_6.graphics.f("#000000").s().p("AAQA2IgZgoIgJAJIAAAfIgNAAIAAhrIANAAIAAA9IAegfIARAAIgdAcIAgAxg");
  this.shape_6.setTransform(892.1,136.1);

  this.shape_7 = new cjs.Shape();
  this.shape_7.graphics.f("#000000").s().p("AgiAsQgKgLAAgTQAAgUAMgLQAKgJAPAAQAPAAAKALQAIAIACAMQAFgDACgDQACgDAAgHIgHAAIAAgPIAPAAIAAANQAAAIgDAFQgEAGgKAFIAAACQAAAcgRAJQgJAFgJAAQgRAAgKgLgAgYgHQgGAHAAAOQAAAPAGAHQAHAIAKAAQAJAAAHgIQAGgHAAgPQAAgOgHgHQgGgIgJABQgKgBgHAIgAgJgiIgRgUIASAAIAJAUg");
  this.shape_7.setTransform(878.8,136.2);

  this.shape_8 = new cjs.Shape();
  this.shape_8.graphics.f("#000000").s().p("AgGA2IAAhNIANAAIAABNgAgGglIAAgQIANAAIAAAQg");
  this.shape_8.setTransform(872.2,136.1);

  this.shape_9 = new cjs.Shape();
  this.shape_9.graphics.f("#000000").s().p("AgXAxQgIgGAAgMIANABQABAGAEADQAEADAJAAQAIAAAFgDQAFgEACgGQABgFAAgMQgJAKgMAAQgQAAgJgLQgIgMAAgPQAAgLAEgKQAEgKAHgEQAIgFAKgBQANAAAJAMIAAgKIAMAAIAABCQAAATgEAHQgDAIgIAEQgIAFgLAAQgOgBgJgGgAgOglQgGAIAAAOQAAAPAGAGQAGAHAIAAQAKAAAGgHQAGgGAAgPQAAgOgGgIQgHgGgJAAQgIAAgGAGg");
  this.shape_9.setTransform(866.1,139.1);

  this.shape_10 = new cjs.Shape();
  this.shape_10.graphics.f("#000000").s().p("AgbA1IgCgMIAHABQAFAAADgBQACgCABgDIAFgJIABgEIgdhNIANAAIARAuIAFARQACgJADgIIAQguIAOAAIgeBPQgEAMgCAFQgEAHgEADQgFADgGAAIgIgCg");
  this.shape_10.setTransform(854.3,139.1);

  this.shape_11 = new cjs.Shape();
  this.shape_11.graphics.f("#000000").s().p("AgcAxQgHgHAAgJQAAgGADgFQACgEAFgDQAEgDAFgCIAMgCQAPgBAHgDIAAgEQAAgHgDgDQgFgFgKABQgJAAgEADQgFADgCAIIgNgCQACgIAEgFQAEgFAIgDQAHgDAKAAQAJAAAHADQAGACADADQADAFABAEIABAMIAAASQAAASABAEQAAAFADAFIgOAAQgCgEgBgGQgHAGgHADQgFADgIAAQgNAAgHgGgAgCATIgMADQgDABgCADQgCADAAADQAAAGAEADQAEAEAIgBQAHABAFgEQAGgDADgGQACgEAAgJIAAgFQgHADgNACgAAIgiIgIgMIgHAMIgPAAIAQgUIANAAIAQAUg");
  this.shape_11.setTransform(846.3,136.2);

  this.shape_12 = new cjs.Shape();
  this.shape_12.graphics.f("#000000").s().p("AgVArIAAAKIgMAAIAAhrIANAAIAAAnQAJgLAMAAQAHAAAGADQAHADAEAFQAEAFACAIQADAGAAAJQAAAUgKALQgKALgOAAQgNAAgIgMgAgPgHQgGAHAAANQAAAOAEAGQAGALALAAQAHAAAHgIQAGgHAAgPQAAgOgGgIQgGgHgIAAQgIAAgHAIg");
  this.shape_12.setTransform(838.2,136.2);

  this.shape_13 = new cjs.Shape();
  this.shape_13.graphics.f("#000000").s().p("AgbA1IgCgMIAHABQAFAAACgBQADgCABgDIAFgJIABgEIgdhNIANAAIARAuIAFARQACgJADgIIAQguIAOAAIgeBPQgEAMgCAFQgEAHgEADQgFADgGAAIgIgCg");
  this.shape_13.setTransform(826,139.1);

  this.shape_14 = new cjs.Shape();
  this.shape_14.graphics.f("#000000").s().p("AgcAiQgHgGAAgKQAAgFADgFQACgFAFgDQAEgCAFgBIAMgCQAPgCAHgDIAAgDQAAgIgDgDQgFgFgKAAQgJAAgEADQgFAEgCAIIgNgCQACgIAEgFQAEgFAIgDQAHgDAKAAQAJAAAHADQAGACADADQADAEABAFIABANIAAAQQAAATABAEQAAAFADAFIgOAAQgCgEgBgGQgHAGgHADQgFADgIAAQgNAAgHgHgAgCAFIgMACQgDACgCADQgCACAAAEQAAAFAEAEQAEADAIAAQAHAAAFgDQAGgDADgGQACgFAAgIIAAgFQgHADgNACg");
  this.shape_14.setTransform(817.9,137.6);

  this.shape_15 = new cjs.Shape();
  this.shape_15.graphics.f("#000000").s().p("AgXAxQgIgGAAgMIANABQABAGAEADQAEADAJAAQAIAAAFgDQAFgEACgGQABgFAAgMQgJAKgMAAQgQAAgJgLQgIgMAAgPQAAgLAEgKQAEgKAHgEQAIgFAKgBQANAAAJAMIAAgKIAMAAIAABCQAAATgEAHQgDAIgIAEQgIAFgLAAQgOgBgJgGgAgOglQgGAIAAAOQAAAPAGAGQAGAHAIAAQAKAAAGgHQAGgGAAgPQAAgOgGgIQgHgGgJAAQgIAAgGAGg");
  this.shape_15.setTransform(809.4,139.1);

  this.shape_16 = new cjs.Shape();
  this.shape_16.graphics.f("#000000").s().p("AASAoIAAgvQAAgIgBgEQgBgEgEgCQgFgCgFAAQgHAAgGAFQgHAFAAAPIAAAqIgMAAIAAhNIALAAIAAALQAJgNAPAAQAHAAAGADQAGACADAEQACAEACAGIABANIAAAvg");
  this.shape_16.setTransform(801.3,137.5);

  this.shape_17 = new cjs.Shape();
  this.shape_17.graphics.f("#000000").s().p("AgXAeQgKgKAAgUQAAgLAEgKQAEgJAJgFQAJgFAJAAQAMAAAIAHQAIAGADAMIgNACQgCgIgFgEQgEgEgHAAQgJAAgGAHQgGAHAAAPQAAAPAGAIQAGAHAIAAQAIAAAFgFQAGgFABgKIANACQgCANgJAIQgJAIgNAAQgOAAgKgLg");
  this.shape_17.setTransform(789.4,137.6);

  this.shape_18 = new cjs.Shape();
  this.shape_18.graphics.f("#000000").s().p("AgHA0IAAgPIAOAAIAAAPgAgaATQgKgKAAgTQAAgWAMgKQAKgJAOAAQAQAAAKALQAKAKAAATQAAAOgEAIQgFAJgIAFQgJAFgKAAQgQAAgKgLgAgQggQgGAHAAAPQAAAOAGAHQAHAIAJAAQAKAAAHgIQAGgHAAgPQAAgOgGgHQgIgIgJAAQgJAAgHAIg");
  this.shape_18.setTransform(781.3,138.7);

  this.shape_19 = new cjs.Shape();
  this.shape_19.graphics.f("#000000").s().p("AgVAxQgIgFgFgJQgEgJAAgMQAAgMAEgIQAEgKAHgFQAIgFAKAAQAGAAAGADQAFADAEAFIAAgVIgZAAIAAgJIAZAAIAAgJIAMAAIAAAJIALAAIAAAJIgLAAIAABZIgLAAIAAgKQgIAMgOAAQgIAAgIgGgAgSgIQgHAHAAAPQABAPAGAHQAGAIAJAAQAIAAAFgHQAHgHAAgPQAAgPgHgHQgGgIgHAAQgKAAgFAHg");
  this.shape_19.setTransform(773.2,136.2);

  this.shape_20 = new cjs.Shape();
  this.shape_20.graphics.f("#000000").s().p("AATAoIAAgvQgBgIgBgEQgCgEgEgCQgDgCgGAAQgHAAgHAFQgFAFAAAPIAAAqIgOAAIAAhNIANAAIAAALQAIgNAPAAQAHAAAGADQAGACADAEQACAEACAGIAAANIAAAvg");
  this.shape_20.setTransform(940.3,118.7);

  this.shape_21 = new cjs.Shape();
  this.shape_21.graphics.f("#000000").s().p("AgZA5QgLgKABgUQgBgVAMgKQAKgJAOAAQAQAAAKALQAKAKAAASQAAAPgEAIQgEAJgJAFQgJAFgKAAQgPAAgKgLgAgQAFQgGAHAAAPQAAAPAGAHQAHAIAJAAQAKAAAHgIQAGgHAAgPQAAgPgGgHQgIgHgJABQgJgBgHAHgAAIgUIgIgNIgHANIgQAAIAQgVIAOAAIAQAVgAgHguIAJgVIASAAIgRAVg");
  this.shape_21.setTransform(931.9,116.1);

  this.shape_22 = new cjs.Shape();
  this.shape_22.graphics.f("#000000").s().p("AgQAlQgHgDgCgEQgEgDgBgHIgBgMIAAgvIAOAAIAAAqIABAOQABAFAEAEQAEACAGAAQAFAAAFgCQAFgDACgGQADgFAAgKIAAgpIAMAAIAABNIgLAAIAAgLQgJANgPAAQgHAAgFgDg");
  this.shape_22.setTransform(923.5,118.9);

  this.shape_23 = new cjs.Shape();
  this.shape_23.graphics.f("#000000").s().p("AAnAoIAAgwIgBgMQgBgDgEgCQgDgCgEAAQgJAAgFAFQgFAGAAAMIAAAsIgNAAIAAgyQAAgJgDgEQgDgEgHAAQgGAAgFADQgEACgDAGQgCAGAAAKIAAAoIgNAAIAAhNIAMAAIAAALQAEgGAGgDQAGgEAIAAQAIAAAGAEQAEADACAHQAKgOAPAAQALAAAGAHQAHAGAAANIAAA1g");
  this.shape_23.setTransform(913.2,118.7);

  this.shape_24 = new cjs.Shape();
  this.shape_24.graphics.f("#000000").s().p("AgaAsQgJgKgBgUQAAgUANgLQAJgJAOAAQAQAAAKALQALALgBARQABAPgFAIQgEAJgJAFQgJAFgKAAQgPAAgLgLgAgPgHQgHAHAAAOQAAAPAHAHQAGAIAJAAQAKAAAGgIQAHgHAAgPQAAgOgHgHQgGgIgKABQgJgBgGAIgAgGghIAJgVIARAAIgQAVg");
  this.shape_24.setTransform(898.6,117.4);

  this.shape_25 = new cjs.Shape();
  this.shape_25.graphics.f("#000000").s().p("AgXAeQgKgKAAgUQAAgLAEgKQAEgJAJgFQAJgFAJAAQAMAAAIAHQAIAGADAMIgNACQgCgIgFgEQgEgEgHAAQgJAAgGAHQgGAHAAAPQAAAPAGAIQAGAHAIAAQAIAAAFgFQAGgFABgKIANACQgCANgJAIQgJAIgNAAQgOAAgKgLg");
  this.shape_25.setTransform(890.9,118.8);

  this.shape_26 = new cjs.Shape();
  this.shape_26.graphics.f("#000000").s().p("AgHBCIAAgPIAPAAIAAAPgAgGAqIAAhNIANAAIAABNgAgGgxIAAgQIANAAIAAAQg");
  this.shape_26.setTransform(881.1,118.5);

  this.shape_27 = new cjs.Shape();
  this.shape_27.graphics.f("#000000").s().p("AASA2IAAgyQABgIgEgFQgFgEgIAAQgFAAgFADQgFADgCAFQgDAEABAJIAAArIgOAAIAAhrIAOAAIAAAnQAJgLANAAQAJAAAGAEQAGADADAGQACAGAAAKIAAAyg");
  this.shape_27.setTransform(875.2,117.3);

  this.shape_28 = new cjs.Shape();
  this.shape_28.graphics.f("#000000").s().p("AgXAeQgKgKAAgUQAAgLAEgKQAEgJAJgFQAJgFAJAAQAMAAAIAHQAIAGADAMIgNACQgCgIgFgEQgEgEgHAAQgJAAgGAHQgGAHAAAPQAAAPAGAIQAGAHAIAAQAIAAAFgFQAGgFABgKIANACQgCANgJAIQgJAIgNAAQgOAAgKgLg");
  this.shape_28.setTransform(867.5,118.8);

  this.shape_29 = new cjs.Shape();
  this.shape_29.graphics.f("#000000").s().p("AgUA3IAehtIALAAIgeBtg");
  this.shape_29.setTransform(861.5,117.3);

  this.shape_30 = new cjs.Shape();
  this.shape_30.graphics.f("#000000").s().p("AATA2IAAgyQgBgIgEgFQgEgEgIAAQgFAAgFADQgFADgCAFQgCAEAAAJIAAArIgOAAIAAhrIAOAAIAAAnQAJgLANAAQAIAAAHAEQAGADADAGQADAGgBAKIAAAyg");
  this.shape_30.setTransform(851.1,117.3);

  this.shape_31 = new cjs.Shape();
  this.shape_31.graphics.f("#000000").s().p("AATAoIAAgvQgBgIgBgEQgCgEgEgCQgDgCgGAAQgHAAgHAFQgFAFAAAPIAAAqIgOAAIAAhNIANAAIAAALQAIgNAPAAQAHAAAGADQAGACADAEQACAEACAGIAAANIAAAvg");
  this.shape_31.setTransform(842.7,118.7);

  this.shape_32 = new cjs.Shape();
  this.shape_32.graphics.f("#000000").s().p("AAiA2IgNghIgsAAIgLAhIgPAAIAphrIAOAAIAsBrgAgHgVIgLAfIAjAAIgLgdIgHgWQgCAKgEAKg");
  this.shape_32.setTransform(833.6,117.3);

  this.shape_33 = new cjs.Shape();
  this.shape_33.graphics.f("#000000").s().p("AgHAIIAAgPIAPAAIAAAPg");
  this.shape_33.setTransform(823.2,121.9);

  this.shape_34 = new cjs.Shape();
  this.shape_34.graphics.f("#000000").s().p("AgbA1IgCgMIAHABQAFAAACgBQADgCACgDIAEgJIABgEIgdhNIAOAAIAQAuIAEARQACgJAEgIIAQguIAOAAIgeBPQgEAMgCAFQgEAHgEADQgFADgGAAIgIgCg");
  this.shape_34.setTransform(818.5,120.3);

  this.shape_35 = new cjs.Shape();
  this.shape_35.graphics.f("#000000").s().p("AgBAzQgEgDgBgDQgCgEAAgMIAAgrIgKAAIAAgLIAKAAIAAgTIAMgIIAAAbIANAAIAAALIgNAAIAAAsIAAAHIADADIAFAAIAFAAIACAMIgKABQgHAAgDgCg");
  this.shape_35.setTransform(812.7,117.5);

  this.shape_36 = new cjs.Shape();
  this.shape_36.graphics.f("#000000").s().p("AgXAxQgIgGAAgNIANACQABAGAEADQAEAEAJAAQAIAAAFgEQAFgEACgHQABgDAAgNQgJAKgMAAQgQAAgJgLQgIgMAAgPQAAgLAEgKQAEgKAHgFQAIgEAKgBQANAAAJAMIAAgKIAMAAIAABCQAAASgEAIQgDAIgIAEQgIAEgLABQgOAAgJgHgAgOglQgGAIAAAOQAAAPAGAGQAGAHAIAAQAKAAAGgHQAGgGAAgPQAAgOgGgIQgHgGgJAAQgIAAgGAGg");
  this.shape_36.setTransform(802,120.3);

  this.shape_37 = new cjs.Shape();
  this.shape_37.graphics.f("#000000").s().p("AASAoIAAgvQABgIgCgEQgBgEgFgCQgEgCgFAAQgHAAgGAFQgHAFAAAPIAAAqIgMAAIAAhNIAMAAIAAALQAIgNAPAAQAHAAAGADQAGACADAEQADAEABAGIABANIAAAvg");
  this.shape_37.setTransform(793.9,118.7);

  this.shape_38 = new cjs.Shape();
  this.shape_38.graphics.f("#000000").s().p("AgaAsQgJgKgBgUQABgUAMgLQAJgJAOAAQAQAAAKALQAKALAAARQABAPgFAIQgEAJgJAFQgJAFgKAAQgPAAgLgLgAgQgHQgGAHAAAOQAAAPAGAHQAHAIAJAAQAKAAAGgIQAHgHAAgPQAAgOgHgHQgHgIgJABQgJgBgHAIgAAIghIgIgNIgHANIgPAAIAPgVIANAAIARAVg");
  this.shape_38.setTransform(785.5,117.4);

  this.shape_39 = new cjs.Shape();
  this.shape_39.graphics.f("#000000").s().p("AgXAeQgKgKAAgUQAAgLAEgKQAEgJAJgFQAJgFAJAAQAMAAAIAHQAIAGADAMIgNACQgCgIgFgEQgEgEgHAAQgJAAgGAHQgGAHAAAPQAAAPAGAIQAGAHAIAAQAIAAAFgFQAGgFABgKIANACQgCANgJAIQgJAIgNAAQgOAAgKgLg");
  this.shape_39.setTransform(777.8,118.8);

  this.shape_40 = new cjs.Shape();
  this.shape_40.graphics.f("#000000").s().p("AgcA0QgGgCgDgEQgDgFgBgGIgBgMIAAgvIANAAIAAArIABANQACALANAAQAGAAAFgDQAEgCADgGQACgFAAgKIAAgpIANAAIAAAjQAJgBAFgIQACgCAAgJIgHAAIAAgPIAOAAIAAAMQAAAMgDAEQgGALgOABIAAAlIgMAAIAAgMQgIAOgQAAQgGAAgGgDgAgMghIgRgVIASAAIAKAVg");
  this.shape_40.setTransform(934,98.6);

  this.shape_41 = new cjs.Shape();
  this.shape_41.graphics.f("#000000").s().p("AgBAzQgEgDgBgDQgCgEAAgMIAAgrIgKAAIAAgLIAKAAIAAgTIAMgIIAAAbIANAAIAAALIgNAAIAAAsIAAAHIADADIAEAAIAGAAIACAMIgKABQgHAAgDgCg");
  this.shape_41.setTransform(926.8,98.7);

  this.shape_42 = new cjs.Shape();
  this.shape_42.graphics.f("#000000").s().p("AgaAeQgKgKAAgUQAAgVAMgKQAKgJAOAAQAQAAAKALQAKAKAAATQAAAOgEAIQgFAJgIAFQgJAFgKAAQgQAAgKgLgAgQgVQgGAHAAAOQAAAPAGAHQAHAIAJAAQAKAAAHgIQAGgHAAgPQAAgOgGgHQgIgIgJAAQgJAAgHAIg");
  this.shape_42.setTransform(916.3,100);

  this.shape_43 = new cjs.Shape();
  this.shape_43.graphics.f("#000000").s().p("AgcAxQgHgHAAgKQAAgFADgFQACgFAFgDQAEgDAFgBIAMgCQAPgBAHgDIAAgEQAAgHgDgDQgFgFgKABQgJAAgEACQgFAEgCAIIgNgCQACgIAEgFQAEgFAIgDQAHgCAKAAQAJAAAHACQAGACADADQADAFABAEIABAMIAAASQAAASABAEQAAAGADAEIgOAAQgCgEgBgGQgHAHgHACQgFADgIAAQgNAAgHgGgAgCATIgMADQgDABgCADQgCACAAAEQAAAFAEAEQAEADAIAAQAHAAAFgDQAGgDADgGQACgFAAgIIAAgFQgHADgNACgAgGghIAIgVIASAAIgQAVg");
  this.shape_43.setTransform(907.9,98.6);

  this.shape_44 = new cjs.Shape();
  this.shape_44.graphics.f("#000000").s().p("AgVArIAAAKIgMAAIAAhrIANAAIAAAnQAJgLAMAAQAHAAAGADQAHADAEAFQAEAFACAIQADAGAAAJQAAAUgKALQgKALgOAAQgNAAgIgMgAgPgHQgGAHAAANQAAAOAEAGQAGALALAAQAHAAAHgIQAGgHAAgPQAAgOgGgIQgGgHgIAAQgIAAgHAIg");
  this.shape_44.setTransform(899.8,98.6);

  this.shape_45 = new cjs.Shape();
  this.shape_45.graphics.f("#000000").s().p("AgXAxQgIgGAAgNIANACQABAHAEACQAEAEAJAAQAIAAAFgEQAFgDACgIQABgDAAgNQgJAKgMAAQgQAAgJgMQgIgLAAgPQAAgMAEgJQAEgJAHgGQAIgEAKAAQANAAAJALIAAgKIAMAAIAABCQAAATgEAHQgDAIgIAEQgIAEgLABQgOAAgJgHgAgOgkQgGAGAAAPQAAAPAGAGQAGAHAIAAQAKAAAGgHQAGgGAAgPQAAgOgGgHQgHgIgJABQgIgBgGAIg");
  this.shape_45.setTransform(886.8,101.5);

  this.shape_46 = new cjs.Shape();
  this.shape_46.graphics.f("#000000").s().p("AATAoIAAgvQgBgIgBgEQgCgEgEgCQgDgCgGAAQgHAAgHAFQgFAFAAAPIAAAqIgOAAIAAhNIANAAIAAALQAIgNAPAAQAHAAAGADQAGACADAEQACAEACAGIAAANIAAAvg");
  this.shape_46.setTransform(878.7,99.9);

  this.shape_47 = new cjs.Shape();
  this.shape_47.graphics.f("#000000").s().p("AgaAsQgKgKABgUQgBgUAMgLQAKgJAOABQAQgBAKALQAKALAAARQAAAPgEAJQgFAIgIAFQgJAFgKAAQgQAAgKgLgAgQgHQgGAHAAAOQAAAPAGAIQAHAHAJAAQAKAAAHgHQAGgIAAgPQAAgOgGgHQgIgHgJAAQgJAAgHAHgAAIghIgIgNIgHANIgQAAIAQgVIAOAAIAQAVg");
  this.shape_47.setTransform(870.4,98.6);

  this.shape_48 = new cjs.Shape();
  this.shape_48.graphics.f("#000000").s().p("AASA2IAAgyQAAgIgDgFQgFgEgIAAQgEAAgGADQgFADgCAFQgCAEgBAJIAAArIgMAAIAAhrIAMAAIAAAnQAKgLANAAQAIAAAHAEQAGADADAGQACAGABAKIAAAyg");
  this.shape_48.setTransform(862,98.5);

  this.shape_49 = new cjs.Shape();
  this.shape_49.graphics.f("#000000").s().p("AgBAzQgEgDgCgDQgBgEAAgMIAAgrIgKAAIAAgLIAKAAIAAgTIAMgIIAAAbIANAAIAAALIgNAAIAAAsIAAAHIADADIAEAAIAGAAIACAMIgKABQgHAAgDgCg");
  this.shape_49.setTransform(855.9,98.7);

  this.shape_50 = new cjs.Shape();
  this.shape_50.graphics.f("#000000").s().p("AgaAsQgJgKgBgUQAAgUANgLQAJgJAOABQAQgBAKALQAKALAAARQABAPgFAJQgFAIgIAFQgJAFgKAAQgPAAgLgLgAgQgHQgGAHAAAOQAAAPAGAIQAHAHAJAAQAKAAAGgHQAHgIAAgPQAAgOgHgHQgHgHgJAAQgJAAgHAHgAgHghIAKgVIAQAAIgPAVg");
  this.shape_50.setTransform(845.4,98.6);

  this.shape_51 = new cjs.Shape();
  this.shape_51.graphics.f("#000000").s().p("AgXAeQgKgKAAgUQAAgLAEgKQAEgJAJgFQAJgFAJAAQAMAAAIAHQAIAGADAMIgNACQgCgIgFgEQgEgEgHAAQgJAAgGAHQgGAHAAAPQAAAPAGAIQAGAHAIAAQAIAAAFgFQAGgFABgKIANACQgCANgJAIQgJAIgNAAQgOAAgKgLg");
  this.shape_51.setTransform(837.7,100);

  this.shape_52 = new cjs.Shape();
  this.shape_52.graphics.f("#000000").s().p("AgHBCIAAgPIAPAAIAAAPgAgGAqIAAhNIANAAIAABNgAgGgxIAAgQIANAAIAAAQg");
  this.shape_52.setTransform(827.9,99.7);

  this.shape_53 = new cjs.Shape();
  this.shape_53.graphics.f("#000000").s().p("AATA2IAAgyQAAgIgFgFQgEgEgIAAQgEAAgGADQgFADgCAFQgDAEABAJIAAArIgOAAIAAhrIAOAAIAAAnQAJgLANAAQAIAAAHAEQAGADADAGQACAGAAAKIAAAyg");
  this.shape_53.setTransform(822,98.5);

  this.shape_54 = new cjs.Shape();
  this.shape_54.graphics.f("#000000").s().p("AgXAeQgKgKAAgUQAAgLAEgKQAEgJAJgFQAJgFAJAAQAMAAAIAHQAIAGADAMIgNACQgCgIgFgEQgEgEgHAAQgJAAgGAHQgGAHAAAPQAAAPAGAIQAGAHAIAAQAIAAAFgFQAGgFABgKIANACQgCANgJAIQgJAIgNAAQgOAAgKgLg");
  this.shape_54.setTransform(814.3,100);

  this.shape_55 = new cjs.Shape();
  this.shape_55.graphics.f("#000000").s().p("AATA2IAAgyQAAgIgFgFQgEgEgIAAQgEAAgGADQgFADgCAFQgCAEAAAJIAAArIgOAAIAAhrIAOAAIAAAnQAJgLANAAQAIAAAHAEQAGADADAGQACAGAAAKIAAAyg");
  this.shape_55.setTransform(802,98.5);

  this.shape_56 = new cjs.Shape();
  this.shape_56.graphics.f("#000000").s().p("AATAoIAAgvQgBgIgBgEQgCgEgEgCQgDgCgGAAQgHAAgHAFQgFAFAAAPIAAAqIgOAAIAAhNIANAAIAAALQAIgNAPAAQAHAAAGADQAGACADAEQACAEACAGIAAANIAAAvg");
  this.shape_56.setTransform(793.7,99.9);

  this.shape_57 = new cjs.Shape();
  this.shape_57.graphics.f("#000000").s().p("AAiA2IgNghIgsAAIgLAhIgPAAIAphrIAOAAIAsBrgAgHgVIgLAfIAjAAIgLgdIgHgWQgCAKgEAKg");
  this.shape_57.setTransform(784.5,98.5);

  this.shape_58 = new cjs.Shape();
  this.shape_58.graphics.f("#000000").s().p("AgGA2IAAgPIAOAAIAAAPgAgDAbIgEg4IAAgYIAPAAIAAAYIgEA4g");
  this.shape_58.setTransform(915.1,79.7);

  this.shape_59 = new cjs.Shape();
  this.shape_59.graphics.f("#000000").s().p("AgGBCIAAgPIANAAIAAAPgAgFAqIAAhNIALAAIAABNgAgFgxIAAgQIALAAIAAAQg");
  this.shape_59.setTransform(911.3,80.9);

  this.shape_60 = new cjs.Shape();
  this.shape_60.graphics.f("#000000").s().p("AATA2IAAgyQgBgIgEgFQgEgEgIAAQgFAAgFADQgFADgCAFQgDAEAAAJIAAArIgMAAIAAhrIAMAAIAAAnQAKgLANAAQAJAAAGAEQAGADADAGQACAGAAAKIAAAyg");
  this.shape_60.setTransform(905.4,79.7);

  this.shape_61 = new cjs.Shape();
  this.shape_61.graphics.f("#000000").s().p("AgXAeQgKgKAAgUQAAgLAEgKQAEgJAJgFQAJgFAJAAQAMAAAIAHQAIAGADAMIgNACQgCgIgFgEQgEgEgHAAQgJAAgGAHQgGAHAAAPQAAAPAGAIQAGAHAIAAQAIAAAFgFQAGgFABgKIANACQgCANgJAIQgJAIgNAAQgOAAgKgLg");
  this.shape_61.setTransform(897.7,81.2);

  this.shape_62 = new cjs.Shape();
  this.shape_62.graphics.f("#000000").s().p("AgUA3IAehtIALAAIgeBtg");
  this.shape_62.setTransform(891.7,79.7);

  this.shape_63 = new cjs.Shape();
  this.shape_63.graphics.f("#000000").s().p("AASA2IAAgyQAAgIgDgFQgFgEgIAAQgEAAgGADQgFADgCAFQgCAEgBAJIAAArIgMAAIAAhrIAMAAIAAAnQAKgLANAAQAJAAAGAEQAGADADAGQACAGABAKIAAAyg");
  this.shape_63.setTransform(885.4,79.7);

  this.shape_64 = new cjs.Shape();
  this.shape_64.graphics.f("#000000").s().p("AATAoIAAgvQAAgIgCgEQgBgEgEgCQgFgCgFAAQgHAAgHAFQgFAFgBAPIAAAqIgNAAIAAhNIAMAAIAAALQAJgNAPAAQAHAAAGADQAGACADAEQACAEACAGIABANIAAAvg");
  this.shape_64.setTransform(877.1,81.1);

  this.shape_65 = new cjs.Shape();
  this.shape_65.graphics.f("#000000").s().p("AAiA2IgNghIgsAAIgLAhIgPAAIAphrIAOAAIAsBrgAgHgVIgLAfIAjAAIgLgdIgHgWQgCAKgEAKg");
  this.shape_65.setTransform(867.9,79.7);

  this.shape_66 = new cjs.Shape();
  this.shape_66.graphics.f("#000000").s().p("AgZAeQgLgKABgUQgBgVAMgKQAKgJAOAAQAQAAAKALQAKAKAAATQAAAOgEAIQgEAJgJAFQgJAFgKAAQgPAAgKgLgAgQgVQgGAHAAAOQAAAPAGAHQAHAIAJAAQAKAAAHgIQAGgHAAgPQAAgOgGgHQgIgIgJAAQgJAAgHAIg");
  this.shape_66.setTransform(855.4,81.2);

  this.shape_67 = new cjs.Shape();
  this.shape_67.graphics.f("#000000").s().p("AgcAwQgHgGAAgKQAAgFADgFQACgFAFgDQAEgDAFgBIAMgCQAPgCAHgDIAAgDQAAgHgDgDQgFgEgKAAQgJAAgEACQgFAEgCAIIgNgCQACgIAEgFQAEgFAIgDQAHgCAKAAQAJAAAHACQAGACADAEQADADABAGIABALIAAASQAAASABAEQAAAGADAEIgOAAQgCgEgBgGQgHAHgHACQgFADgIAAQgNAAgHgHgAgCATIgMADQgDABgCADQgCACAAAEQAAAFAEAEQAEAEAIAAQAHAAAFgEQAGgDADgGQACgFAAgIIAAgFQgHADgNACgAAAghIgPgVIAQAAIAKAVg");
  this.shape_67.setTransform(847,79.8);

  this.shape_68 = new cjs.Shape();
  this.shape_68.graphics.f("#000000").s().p("AASA2IAAgyQAAgIgEgFQgEgEgIAAQgFAAgFADQgFADgCAFQgDAEAAAJIAAArIgMAAIAAhrIAMAAIAAAnQAKgLANAAQAJAAAGAEQAGADADAGQACAGABAKIAAAyg");
  this.shape_68.setTransform(838.7,79.7);

  this.shape_69 = new cjs.Shape();
  this.shape_69.graphics.f("#000000").s().p("AgXAeQgKgKAAgUQAAgLAEgKQAEgJAJgFQAJgFAJAAQAMAAAIAHQAIAGADAMIgNACQgCgIgFgEQgEgEgHAAQgJAAgGAHQgGAHAAAPQAAAPAGAIQAGAHAIAAQAIAAAFgFQAGgFABgKIANACQgCANgJAIQgJAIgNAAQgOAAgKgLg");
  this.shape_69.setTransform(831,81.2);

  this.shape_70 = new cjs.Shape();
  this.shape_70.graphics.f("#000000").s().p("AASAoIAAgvQABgIgCgEQgCgEgEgCQgEgCgFAAQgHAAgGAFQgHAFAAAPIAAAqIgMAAIAAhNIALAAIAAALQAJgNAPAAQAHAAAGADQAGACADAEQADAEABAGIABANIAAAvg");
  this.shape_70.setTransform(818.7,81.1);

  this.shape_71 = new cjs.Shape();
  this.shape_71.graphics.f("#000000").s().p("AgGA2IAAhNIANAAIAABNgAgGglIAAgQIANAAIAAAQg");
  this.shape_71.setTransform(812.9,79.7);

  this.shape_72 = new cjs.Shape();
  this.shape_72.graphics.f("#000000").s().p("AAgA2IgbgnIgFgHIgEAIIgbAmIgRAAIApg3Igkg0IARAAIATAcIAIANIAIgMIAWgdIAPAAIglAzIAoA4g");
  this.shape_72.setTransform(806.2,79.7);

  this.shape_73 = new cjs.Shape();
  this.shape_73.graphics.f("#000000").s().p("AgCA0QgFgDgCgDQgCgCgBgGQgBgDAAgLIAAghIgJAAIAAgRIAJAAIAAgPIAUgMIAAAbIAOAAIAAARIgOAAIAAAeIABAMQAAAAAAAAQAAABAAAAQAAAAABAAQAAABAAAAIAEABIAIgCIACAQQgHADgJAAQgGABgDgCg");
  this.shape_73.setTransform(921.9,117.5);

  this.shape_74 = new cjs.Shape();
  this.shape_74.graphics.f("#000000").s().p("AgXA0QgGgEgDgGQgCgGAAgLIAAgxIAUAAIAAAjQAAARABADQABAFADABQADACAFAAQAEAAAEgCQAFgDABgFQACgEAAgQIAAghIAUAAIAABOIgTAAIAAgMQgEAGgHAEQgGADgIAAQgHAAgHgDgAgJghIAJgVIAXAAIgUAVg");
  this.shape_74.setTransform(914.8,117.3);

  this.shape_75 = new cjs.Shape();
  this.shape_75.graphics.f("#000000").s().p("AAPA2IAAgpQAAgNgBgCQgBgDgDgCQgDgCgFAAQgEAAgEACQgEADgCAEQgBAFgBAKIAAAnIgUAAIAAhrIAUAAIAAAoQAKgMANAAQAHAAAGADQAFACADAFQADAEACAFIABAOIAAAug");
  this.shape_75.setTransform(905.7,117.3);

  this.shape_76 = new cjs.Shape();
  this.shape_76.graphics.f("#000000").s().p("AglA3IAAhrIATAAIAAAMQAEgGAGgEQAHgEAGAAQAOAAAKALQAJAKAAAUQAAASgJALQgKALgOAAQgFAAgFgDQgGgDgFgGIAAAogAgMgfQgEAFgBAMQABANAEAFQAGAHAGAAQAHgBAGgGQAEgEAAgNQAAgMgFgGQgFgHgHAAQgHAAgFAHg");
  this.shape_76.setTransform(896.8,120.2);

  this.shape_77 = new cjs.Shape();
  this.shape_77.graphics.f("#000000").s().p("AgYArQgKgNAAgeQAAgdALgOQAIgLAPAAQAQAAAIAMQALANAAAdQAAAegLAOQgIALgQAAQgPAAgJgMgAgGgjQgDADgBAGQgCAIAAASQAAATABAHQACAHADACQADADADAAQAEAAADgDQADgCABgGQACgIAAgTQAAgSgCgHQgCgHgCgDQgDgCgEAAQgDAAgDACg");
  this.shape_77.setTransform(883.6,117.4);

  this.shape_78 = new cjs.Shape();
  this.shape_78.graphics.f("#000000").s().p("AADA2IAAhMQgLAKgPAFIAAgTQAJgCAJgIQAIgHAEgKIAQAAIAABrg");
  this.shape_78.setTransform(874.7,117.3);

  this.shape_79 = new cjs.Shape();
  this.shape_79.graphics.f("#000000").s().p("AgaAxQgIgHAAgLIAAgCIAXADQABAEACACQADACAGAAQAIAAAEgCQACgCABgEIABgJIAAgLQgJAMgNAAQgPAAgKgNQgHgLAAgOQAAgUAJgKQAKgLAOAAQAOAAAJANIAAgLIATAAIAABFQAAAOgCAHQgDAHgDAEQgFADgGADQgIACgKAAQgSgBgJgGgAgLghQgFAGAAAMQAAAMAFAFQAFAFAGAAQAHAAAGgFQAFgGAAgLQAAgMgFgGQgFgGgIAAQgGAAgFAGg");
  this.shape_79.setTransform(862.2,120.3);

  this.shape_80 = new cjs.Shape();
  this.shape_80.graphics.f("#000000").s().p("AAPAoIAAgoQAAgMgBgDQgCgEgDgCQgDgCgEAAQgEAAgFADQgEADgBAFQgCAEAAANIAAAjIgVAAIAAhNIATAAIAAALQALgNAOAAQAHAAAGADQAFACADAEIAFAJIAAANIAAAwg");
  this.shape_80.setTransform(853.3,118.7);

  this.shape_81 = new cjs.Shape();
  this.shape_81.graphics.f("#000000").s().p("AgTAkQgKgFgFgJQgFgKAAgMQAAgKAFgKQAFgKAJgFQAJgFALAAQARAAAMAMQALALAAARQAAARgLAMQgMAMgRAAQgKAAgJgFgAgNgRQgFAGAAALQAAALAFAHQAGAGAHAAQAIAAAFgGQAGgHAAgLQAAgLgGgGQgFgGgIAAQgHAAgGAGg");
  this.shape_81.setTransform(844.1,118.8);

  this.shape_82 = new cjs.Shape();
  this.shape_82.graphics.f("#000000").s().p("AgYAoIAAhNIATAAIAAALQAFgIADgCQADgDAFAAQAIAAAGAEIgGASQgGgDgEAAQgEAAgDACQgCACgCAHQgCAGAAATIAAAYg");
  this.shape_82.setTransform(837.2,118.7);

  this.shape_83 = new cjs.Shape();
  this.shape_83.graphics.f("#000000").s().p("AgCA0QgFgDgCgDQgCgCgBgGQgBgDAAgLIAAghIgJAAIAAgRIAJAAIAAgPIAUgMIAAAbIAOAAIAAARIgOAAIAAAeIABAMQAAAAAAAAQAAABAAAAQAAAAABAAQAAABAAAAIAEABIAIgCIACAQQgHADgJAAQgGABgDgCg");
  this.shape_83.setTransform(831.2,117.5);

  this.shape_84 = new cjs.Shape();
  this.shape_84.graphics.f("#000000").s().p("AgdAyQgKgEgFgKQgFgJAAgOQAAgKAFgJQAFgJAJgFQAJgFAMAAQAQAAAMALQAKALABAOQAJgEABgKIgKAAIAAgVIAUAAIAAAQQAAAIgBAEQgCAGgEAEQgFAEgIADQgCANgJAKQgMAMgQgBQgLABgJgGgAgXgCQgFAGAAAKQAAAMAFAGQAGAHAIgBQAIABAEgHQAGgGAAgMQAAgKgGgGQgEgGgIAAQgIAAgGAGgAgMghIgUgVIAXAAIAJAVg");
  this.shape_84.setTransform(819.5,117.3);

  this.shape_85 = new cjs.Shape();
  this.shape_85.graphics.f("#000000").s().p("AAPA2IAAgpQAAgNgBgCQgBgDgEgCQgDgCgEAAQgEAAgEACQgEADgCAEQgBAFAAAKIAAAnIgWAAIAAhrIAWAAIAAAoQAJgMANAAQAHAAAGADQAFACAEAFQACAEABAFIABAOIAAAug");
  this.shape_85.setTransform(809.4,117.3);

  this.shape_86 = new cjs.Shape();
  this.shape_86.graphics.f("#000000").s().p("AggApQgOgPAAgZQAAgaAOgPQAOgOAWAAQATAAANALQAHAHAEAMIgWAGQgCgIgGgGQgGgEgIAAQgLAAgIAJQgHAIAAATQAAAUAHAIQAHAJALAAQAJAAAGgGQAGgFADgMIAVAGQgFASgLAJQgLAIgSAAQgUAAgOgOg");
  this.shape_86.setTransform(799.3,117.3);

  this.shape_87 = new cjs.Shape();
  this.shape_87.graphics.f("#000000").s().p("AggBFIgCgQIAJABQAHAAAEgEQADgFABgGIgdhNIAWAAIASA2IASg2IAVAAIgcBKIgFANQgCAHgCAEIgFAGIgIADIgLABIgLgBgAgKgwIAKgVIAXAAIgUAVg");
  this.shape_87.setTransform(880.3,81.2);

  this.shape_88 = new cjs.Shape();
  this.shape_88.graphics.f("#000000").s().p("AgaAxQgIgHAAgLIAAgCIAYADQAAAEACACQADACAFAAQAIAAAFgCQACgCACgEIAAgJIAAgMQgJANgNAAQgQAAgIgOQgIgKAAgPQAAgTAKgKQAJgKAOAAQANAAAKAMIAAgLIATAAIAABFQAAAOgCAHQgDAGgEAEQgDAEgIADQgGABgLAAQgTAAgIgGgAgLghQgFAFAAAMQAAANAFAEQAFAGAGABQAHgBAGgGQAFgFAAgLQAAgMgFgGQgFgGgIAAQgGAAgFAGg");
  this.shape_88.setTransform(867.3,82.7);

  this.shape_89 = new cjs.Shape();
  this.shape_89.graphics.f("#000000").s().p("AAPAoIAAgoQAAgMgCgDQgBgEgCgCQgDgCgFAAQgEAAgEADQgEADgCAFQgCAEAAANIAAAjIgUAAIAAhNIATAAIAAALQAKgNAPAAQAGAAAGADQAFACADAEIAFAJIABANIAAAwg");
  this.shape_89.setTransform(858.4,81.1);

  this.shape_90 = new cjs.Shape();
  this.shape_90.graphics.f("#000000").s().p("AgTA/QgKgFgFgJQgFgKAAgNQAAgKAFgKQAFgIAJgGQAJgEALAAQARAAAMALQALAKAAASQAAARgLAMQgMAMgRAAQgKAAgJgFgAgNAJQgFAHAAALQAAAMAFAGQAGAGAHAAQAIAAAFgGQAGgGAAgMQAAgLgGgHQgFgGgIAAQgHAAgGAGgAAIgUIgIgLIgHALIgRAAIAPgWIASAAIAQAWgAgCguIgUgVIAWAAIAKAVg");
  this.shape_90.setTransform(849.2,78.5);

  this.shape_91 = new cjs.Shape();
  this.shape_91.graphics.f("#000000").s().p("AgnA2IAAgvIgKAAIAAgNIAKAAIAAgvIAnAAQANAAAIADQAJACAHAIQAHAHADAKQADAKAAAOQAAAOgDAKQgEALgIAHQgFAFgJADQgIADgMAAgAgRAkIAQAAQAKAAAFgCQAGgCADgGQAFgIAAgRQAAgMgCgGQgCgHgDgEQgFgEgFgBQgFgBgNAAIgKAAIAAAcIAWAAIAAANIgWAAg");
  this.shape_91.setTransform(838.8,79.7);

  this.timeline.addTween(cjs.Tween.get({}).to({state:[]}).to({state:[{t:this.shape_72},{t:this.shape_71},{t:this.shape_70},{t:this.shape_69},{t:this.shape_68},{t:this.shape_67},{t:this.shape_66},{t:this.shape_65},{t:this.shape_64},{t:this.shape_63},{t:this.shape_62},{t:this.shape_61},{t:this.shape_60},{t:this.shape_59},{t:this.shape_58},{t:this.shape_57},{t:this.shape_56},{t:this.shape_55},{t:this.shape_54},{t:this.shape_53},{t:this.shape_52},{t:this.shape_51},{t:this.shape_50},{t:this.shape_49},{t:this.shape_48},{t:this.shape_47},{t:this.shape_46},{t:this.shape_45},{t:this.shape_44},{t:this.shape_43},{t:this.shape_42},{t:this.shape_41},{t:this.shape_40},{t:this.shape_39},{t:this.shape_38},{t:this.shape_37},{t:this.shape_36},{t:this.shape_35},{t:this.shape_34},{t:this.shape_33},{t:this.shape_32},{t:this.shape_31},{t:this.shape_30},{t:this.shape_29},{t:this.shape_28},{t:this.shape_27},{t:this.shape_26},{t:this.shape_25},{t:this.shape_24},{t:this.shape_23},{t:this.shape_22},{t:this.shape_21},{t:this.shape_20},{t:this.shape_19},{t:this.shape_18},{t:this.shape_17},{t:this.shape_16},{t:this.shape_15},{t:this.shape_14},{t:this.shape_13},{t:this.shape_12},{t:this.shape_11},{t:this.shape_10},{t:this.shape_9},{t:this.shape_8},{t:this.shape_7},{t:this.shape_6},{t:this.shape_5},{t:this.shape_4},{t:this.shape_3},{t:this.shape_2},{t:this.shape_1},{t:this.shape}]},1).to({state:[{t:this.shape_91},{t:this.shape_90},{t:this.shape_89},{t:this.shape_88},{t:this.shape_87},{t:this.shape_86},{t:this.shape_85},{t:this.shape_84},{t:this.shape_83},{t:this.shape_82},{t:this.shape_81},{t:this.shape_80},{t:this.shape_79},{t:this.shape_78},{t:this.shape_77},{t:this.shape_76},{t:this.shape_75},{t:this.shape_74},{t:this.shape_73}]},19).to({_off:false},9).wait(74));

  // button_đồng ý
  this.shape_92 = new cjs.Shape();
  this.shape_92.graphics.f().s("#6699FF").ss(1,1,1).p("Aj5h9IHzAAQAyAAAAAyIAACXQAAAygyAAInzAAQgyAAAAgyIAAiXQAAgyAyAAg");
  this.shape_92.setTransform(858.8,80.6);

  this.shape_93 = new cjs.Shape();
  this.shape_93.graphics.f("#66FFFF").s().p("Aj5B+QgyAAAAgyIAAiXQAAgyAyAAIHzAAQAyAAAAAyIAACXQAAAygyAAg");
  this.shape_93.setTransform(858.8,80.6);

  this.timeline.addTween(cjs.Tween.get({})
    .to({state:[]}).to({state:[{t:this.shape_93},{t:this.shape_92}]},20)
    .to({_off:false},9).wait(74));

  // button_chờ
  this.shape_94 = new cjs.Shape();
  this.shape_94.graphics.f().s("#99CC00").ss(1,1,1).p("AqJh1IUTAAQAyAAAAAyIAACHQAAAygyAAI0TAAQgyAAAAgyIAAiHQAAgyAyAAg");
  this.shape_94.setTransform(859.6,117.8);

  this.shape_95 = new cjs.Shape();
  this.shape_95.graphics.f("#FFFF99").s().p("AqJB2QgyAAAAgyIAAiHQAAgyAyAAIUTAAQAyAAAAAyIAACHQAAAygyAAg");
  this.shape_95.setTransform(859.6,117.8);

  this.timeline.addTween(cjs.Tween.get({})
    .to({state:[]}).to({state:[{t:this.shape_95},{t:this.shape_94}]},20)
    .to({_off:false},9).wait(74));

  // notice_box
  this.shape_96 = new cjs.Shape();
  this.shape_96.graphics.f().s("#676767").ss(1,1,1).p("AREAAQAAENlAC9Qk/C+nFAAQnEAAlAi+QgRgKgRgLQkdi3AAj+QAAkME/i+QFAi9HEAAQHFAAE/C9QAiAVAeAUQEACyAADvg");
  this.shape_96.setTransform(859,113.2);

  this.shape_97 = new cjs.Shape();
  this.shape_97.graphics.f("#FFFFFF").s().p("AsEHKQghgUgfgVQj/ixAAjwQAAkLE/i+QFAi+HEAAQHEAAFAC+IAiAVQEACxAADwQAAEMlAC9Qk/C+nEAAQnEAAlBi+IgigVIAiAVQFBC+HEAAQHEAAE/i+QFAi9AAkMQAAjwkAixQEeC3AAD9QAAENlAC9QlAC+nEAAQnEAAlAi+gAMmm0IAAAAg");
  this.shape_97.setTransform(859,113.2);

  this.shape_98 = new cjs.Shape();
  this.shape_98.graphics.f().s("#676767").ss(1,1,1).p("AREAAQAAENlAC9Qk/C+nFAAQnEAAlAi+QgRgLgQgKQkei3AAj+QAAkME/i+QFAi9HEAAQHFAAE/C9QAhAUAfAVQEACyAADvg");
  this.shape_98.setTransform(859,107.2);

  this.shape_99 = new cjs.Shape();
  this.shape_99.graphics.f("#FFFFFF").s().p("AsEHKQghgUgfgVQj/ixAAjwQAAkME/i9QFAi+HEAAQHEAAFAC+IAiAUQEACyAADwQAAEMlAC9Qk/C+nEAAQnEAAlBi+IgigVIAiAVQFBC+HEAAQHEAAE/i+QFAi9AAkMQAAjwkAiyQEeC4AAD9QAAEMlAC+QlAC+nEAAQnEAAlAi+gAMmm1IAAAAg");
  this.shape_99.setTransform(859,107.2);

  this.timeline.addTween(cjs.Tween.get({}).to({state:[]}).to({state:[{t:this.shape_97},{t:this.shape_96}]},1).to({state:[{t:this.shape_99},{t:this.shape_98}]},19).to({_off:false},9).wait(74));

}).prototype = p_step3 = new cjs.MovieClip();
p_step3.nominalBounds = null;
// lib_step3rary properties:
lib_step3.properties = {
  id: '9D33D0B2ABC183428F26CA73B2961738_3',
  width: 1280,
  height: 720,
  fps: 15,
  color: "#FFFFFF",
  opacity: 1.00,
  manifest: [
    {src:"/bitrix/components/vportal/vivi_notify/templates/.default/images/cute.png?1533277784351", id:"cute"}
  ],
  preloads: []
};

// bootstrap callback support:
(lib_step3.Stage = function(canvas) {
  createjs.Stage.call(this, canvas);
}).prototype = p_step3 = new createjs.Stage();

p_step3.setAutoPlay = function(autoPlay) {
  this.tickEnabled = autoPlay;
}
p_step3.play = function() { this.tickEnabled = true; this.getChildAt(0).gotoAndPlay(this.getTimelinePosition()) }
p_step3.stop = function(ms) { if(ms) this.seek(ms); this.tickEnabled = false; }
p_step3.seek = function(ms) { this.tickEnabled = true; this.getChildAt(0).gotoAndStop(lib_step3.properties.fps * ms / 1000); }
p_step3.getDuration = function() { return this.getChildAt(0).totalFrames / lib_step3.properties.fps * 1000; }

p_step3.getTimelinePosition = function() { return this.getChildAt(0).currentFrame / lib_step3.properties.fps * 1000; }
//STEP3_END
//=============================================================================

//STEP4_BEGIN
var p_step4; // shortcut to reference prototypes
var lib_step4={};var ss_step4={};var img_step4={};
lib_step4.ssMetadata = [
    {name:"notice1_cảnh4_đọc thông báo_atlas_", frames: [[0,0,2279,3031],[0,3033,297,302]]}
];

// symbols:
(lib_step4._1 = function() {
  this.spriteSheet = ss_step4["notice1_cảnh4_đọc thông báo_atlas_"];
  this.gotoAndStop(0);
}).prototype = p_step4 = new cjs.Sprite();

(lib_step4.happy = function() {
  this.spriteSheet = ss_step4["notice1_cảnh4_đọc thông báo_atlas_"];
  this.gotoAndStop(1);
}).prototype = p_step4 = new cjs.Sprite();

// stage content:
(lib_step4.notice1_cảnh4_đọcthôngbáo = function(mode,startPosition,loop) {
  this.initialize(mode,startPosition,loop,{});

  // happy
  this.instance = new lib_step4.happy();
  this.instance.parent = this;
  this.instance.setTransform(815,393,0.445,0.445);

  this.timeline.addTween(cjs.Tween.get(this.instance).wait(25));

  // text
  this.shape = new cjs.Shape();
  this.shape.graphics.f("#FFFFFF").s().p("AgUAYQgIgJAAgPQAAgOAIgJQAIgIANgBQALAAAHAGQAHAEADAKIgRADQAAgFgDgCQgDgDgFAAQgFABgEAEQgDAEAAAKQAAAKADAEQAEAFAFAAQAFAAADgDQADgDABgGIAQADQgCALgHAGQgHAFgMAAQgNAAgIgIg");
  this.shape.setTransform(894.2,561.3);

  this.shape_1 = new cjs.Shape();
  this.shape_1.graphics.f("#FFFFFF").s().p("AgIAsIAAgRIAPAAIAAARgAgQASQgHgFgEgHQgEgHAAgKQAAgIAEgIQAEgHAHgFQAIgEAIAAQAOAAAJAJQAJAJAAAPQAAANgJAJQgJAJgOAAQgHAAgJgDgAgKgZQgFAFAAAKQAAAJAFAEQAFAFAFAAQAHAAAEgFQAEgEAAgJQAAgKgEgFQgEgEgHAAQgFAAgFAEg");
  this.shape_1.setTransform(887.1,562.4);

  this.shape_2 = new cjs.Shape();
  this.shape_2.graphics.f("#FFFFFF").s().p("AgZAjQgIgJAAgPQAAgPAIgIQAIgIAKAAQAKAAAHAIIAAgOIgQAAIAAgLIAQAAIAAgGIARAAIAAAGIAHAAIAAALIgHAAIAABEIgPAAIAAgJQgEAFgFADQgEADgGAAQgKAAgIgJgAgMgDQgEAEAAAJQAAAKADAEQAEAHAHAAQAFAAAEgFQAEgFAAgKQAAgKgEgEQgEgFgFAAQgGAAgEAFg");
  this.shape_2.setTransform(879.9,560.1);

  this.shape_3 = new cjs.Shape();
  this.shape_3.graphics.f("#FFFFFF").s().p("AgWAmQgGgFAAgIQAAgGADgEQACgDAEgDQAFgCAJgBQAKgCAFgCIAAgCQAAgEgDgCQgCgCgGAAQgEAAgCACQgDABgCAFIgPgDQADgJAGgFQAHgEALABQAKAAAGACQAFADACADQACAEAAALIAAATIABALIADAJIgQAAIgBgFIgBgCQgFAEgEACQgEACgFAAQgKAAgFgFgAAAAOQgGACgDABQgDADAAAEQAAADADADQADACADAAQAEAAAEgDQADgDABgCIABgJIAAgDIgKACgAgTgcIgBgCQAAgGAEgDQADgDAEAAIAEAAIAGADIAHABIADAAQAAgBABAAQAAgBAAAAQAAgBAAAAQABgBAAAAIAIAAQgBAHgDAEQgDADgEAAIgEAAIgGgCIgHgCIgEABIgBADg");
  this.shape_3.setTransform(869.4,560.2);

  this.shape_4 = new cjs.Shape();
  this.shape_4.graphics.f("#FFFFFF").s().p("AgZAjQgIgJABgPQgBgPAIgIQAIgIALAAQAJAAAIAIIAAgOIgRAAIAAgLIARAAIAAgGIAQAAIAAAGIAGAAIAAALIgGAAIAABEIgPAAIAAgJQgEAFgFADQgEADgFAAQgLAAgIgJgAgMgDQgEAEAAAJQAAAKADAEQAEAHAHAAQAFAAAEgFQAEgFAAgKQAAgKgEgEQgEgFgFAAQgGAAgEAFg");
  this.shape_4.setTransform(862.6,560.1);

  this.shape_5 = new cjs.Shape();
  this.shape_5.graphics.f("#FFFFFF").s().p("AgSAdQgFgDgCgFQgCgFAAgJIAAgmIAQAAIAAAdQAAAMABADQABADACABQADACADAAQAEAAADgCQADgDABgDQACgDAAgNIAAgaIAQAAIAAA9IgPAAIAAgJQgEAFgFADQgFADgGAAQgGAAgFgDg");
  this.shape_5.setTransform(851.7,561.3);

  this.shape_6 = new cjs.Shape();
  this.shape_6.graphics.f("#FFFFFF").s().p("AgXAxQgFgFAAgIQAAgGACgEQADgEAFgCQAEgCAIgCQALgCAEgCIAAgBQAAgFgCgCQgCgCgFAAQgFAAgDABQgCACgBAFIgPgDQACgIAGgFQAHgEALAAQALAAAFADQAFACADAEQACADAAALIAAATIAAAMIADAJIgQAAIgCgFIgBgCQgDAEgGACQgDACgGAAQgJAAgGgFgAAAAZQgHACgCABQgDADAAADQAAAEADACQADADAEAAQADAAAEgDQADgDABgDIAAgIIAAgDIgJACgAAHgQIgHgJIgGAJIgNAAIAMgSIAPAAIAMASgAgHglIAHgQIASAAIgQAQg");
  this.shape_6.setTransform(844.7,559.1);

  this.shape_7 = new cjs.Shape();
  this.shape_7.graphics.f("#FFFFFF").s().p("AgWAjQgHgJAAgPQAAgPAHgIQAIgIALAAQAJAAAIAIIAAgfIAQAAIAABVIgPAAIAAgJQgEAFgFADQgFADgEAAQgLAAgIgJgAgJgDQgEAEAAAJQAAAKADAEQAEAHAGAAQAGAAAEgFQAEgFAAgKQAAgKgEgEQgEgFgGAAQgFAAgEAFg");
  this.shape_7.setTransform(837.5,560.1);

  this.shape_8 = new cjs.Shape();
  this.shape_8.graphics.f("#FFFFFF").s().p("AAMArIAAghQAAgKgBgCQgBgCgCgBQgDgCgDAAQgDAAgEACQgDACgBADQgCAEAAAIIAAAfIgQAAIAAhVIAQAAIAAAfQAIgJAKABQAGAAAFABQAEACACAEQADADABAEIAAAMIAAAkg");
  this.shape_8.setTransform(827,560.1);

  this.shape_9 = new cjs.Shape();
  this.shape_9.graphics.f("#FFFFFF").s().p("AAMAgIAAggQAAgJgBgDQgBgDgCgCQgDgBgDAAQgDAAgEACQgDACgBAEQgCAEAAAKIAAAcIgQAAIAAg9IAPAAIAAAJQAIgLAMAAQAFAAAFACQAEACACADQADADABAEIAAALIAAAmg");
  this.shape_9.setTransform(819.6,561.2);

  this.shape_10 = new cjs.Shape();
  this.shape_10.graphics.f("#FFFFFF").s().p("AgXAnQgFgFAAgIQAAgGADgEQACgDAFgDQAEgCAIgBQALgCAEgCIAAgCQAAgEgCgCQgCgCgFAAQgFAAgDACQgCABgBAEIgPgCQACgJAGgFQAHgEALABQALAAAFACQAFADADADQACAEAAALIAAATIABALIACAJIgQAAIgCgFIgBgCQgEAEgFACQgDACgGAAQgJAAgGgFgAAAAPQgHACgBABQgEADAAAEQAAADADADQACACAFAAQADAAAEgDQADgDABgCIAAgJIAAgDIgJACgAgIgaIAIgSIASAAIgQASg");
  this.shape_10.setTransform(812.7,560.1);

  this.shape_11 = new cjs.Shape();
  this.shape_11.graphics.f("#FFFFFF").s().p("AgfArIAAglIgIAAIAAgKIAIAAIAAgmIAfAAQALAAAGACQAHACAGAGQAFAFADAJQACAIAAALQAAALgCAJQgEAIgFAFQgFAEgIADQgFACgKAAgAgNAcIANAAQAHAAAEgBQAFgBADgFQAEgHAAgOQAAgIgCgFQgCgGgDgDQgDgDgEgCIgOgBIgIAAIAAAYIARAAIAAAKIgRAAg");
  this.shape_11.setTransform(804.7,560.1);

  this.shape_12 = new cjs.Shape();
  this.shape_12.graphics.f("#666666").s().p("Ag+BwQgcgRgOgdQgPgeAAghQAAg9AigjQAhgkA0AAQAjAAAcARQAcAQAOAeQAPAeAAAkQAAAmgQAfQgPAegcAPQgcAQghAAQgiAAgcgSgAg7hMQgaAYAAA4QAAAtAZAaQAYAaAkAAQAlAAAYgaQAZgaAAgxQAAgdgLgXQgKgXgUgNQgUgMgZAAQgiAAgZAYg");
  this.shape_12.setTransform(784.3,94.3);

  this.shape_13 = new cjs.Shape();
  this.shape_13.graphics.f("#666666").s().p("ABPCdIgdhMIhoAAIgbBMIgjAAIBgj6IAjAAIBmD6gAgQgSIgcBIIBUAAIgahFQgMgfgFgUQgFAYgIAYgAgXhsIAWgwIAoAAIgmAwg");
  this.shape_13.setTransform(758.9,91.1);

  this.shape_14 = new cjs.Shape();
  this.shape_14.graphics.f("#666666").s().p("AheB9IAAj5IBeAAQAcAAASAHQARAIAKAQQAJAQAAARQAAAQgIAOQgJAOgSAJQAXAHAMAPQANAQAAAWQAAARgIAPQgHAPgLAJQgLAIgRAEQgQAEgYAAgAg8BgIA9AAQAQAAAGgCQAMgCAIgEQAHgFAFgJQAFgJAAgMQAAgOgHgLQgHgKgNgEQgNgEgXAAIg5AAgAg8gTIA2AAQAVAAAJgDQANgEAGgIQAHgJAAgNQAAgNgGgJQgGgKgLgDQgLgEgaAAIgyAAg");
  this.shape_14.setTransform(735.9,94.3);

  this.shape_15 = new cjs.Shape();
  this.shape_15.graphics.f("#666666").s().p("Ag2ByQgegQgQgeQgPgeAAglQAAgjAPggQAPggAdgPQAdgQAkAAQAcAAAWAJQAWAJAMAQQANAPAGAaIgeAIQgGgTgIgLQgIgLgQgHQgPgGgUAAQgVAAgRAHQgQAHgKALQgLALgFAOQgKAXAAAbQAAAhAMAWQAMAXAVALQAXALAXAAQAWAAAUgJQAVgIALgJIAAgvIhLAAIAAgcIBrgBIAABcQgZAUgZAKQgaAKgcAAQgkAAgegQg");
  this.shape_15.setTransform(700.5,94.3);

  this.shape_16 = new cjs.Shape();
  this.shape_16.graphics.f("#666666").s().p("ABBB9IiDjEIAADEIggAAIAAj5IAiAAICDDDIAAjDIAgAAIAAD5g");
  this.shape_16.setTransform(674.3,94.3);

  this.shape_17 = new cjs.Shape();
  this.shape_17.graphics.f("#666666").s().p("Ag+COQgcgRgOgeQgPgdAAghQAAg+AigjQAhgjA0AAQAjAAAcAQQAcARAOAeQAPAdAAAlQAAAmgQAeQgPAegcAQQgcAPghAAQgiAAgcgRgAg7guQgaAYAAA3QAAAuAZAaQAYAaAkAAQAlAAAYgbQAZgaAAgwQAAgegLgXQgKgXgUgMQgUgNgZAAQgiAAgZAZgAAShuIgSgdIgTAdIgjAAIAlgwIAgAAIAmAwg");
  this.shape_17.setTransform(648.2,91.3);

  this.shape_18 = new cjs.Shape();
  this.shape_18.graphics.f("#666666").s().p("ABBB9IAAh2IiBAAIAAB2IghAAIAAj5IAhAAIAABnICBAAIAAhnIAhAAIAAD5g");
  this.shape_18.setTransform(621.9,94.3);

  this.shape_19 = new cjs.Shape();
  this.shape_19.graphics.f("#666666").s().p("AgQB9IAAjcIhTAAIAAgdIDGAAIAAAdIhTAAIAADcg");
  this.shape_19.setTransform(598.7,94.3);

  this.timeline.addTween(cjs.Tween.get({}).to({state:[{t:this.shape_19},{t:this.shape_18},{t:this.shape_17},{t:this.shape_16},{t:this.shape_15},{t:this.shape_14},{t:this.shape_13},{t:this.shape_12},{t:this.shape_11},{t:this.shape_10},{t:this.shape_9},{t:this.shape_8},{t:this.shape_7},{t:this.shape_6},{t:this.shape_5},{t:this.shape_4},{t:this.shape_3},{t:this.shape_2},{t:this.shape_1},{t:this.shape}]}).wait(25));

  // button
  this.shape_20 = new cjs.Shape();
  this.shape_20.graphics.f().s("#FF0000").ss(1,1,1).p("AmZhxIM0AAQAuAAAiAiQAhAhAAAuQAAAughAiQgiAhguAAIs0AAQgwAAggghQgigiAAguQAAguAighQAggiAwAAg");
  this.shape_20.setTransform(849.1,560);

  this.shape_21 = new cjs.Shape();
  this.shape_21.graphics.f("#FF6633").s().p("AmZByQgwgBggghQgigiAAguQAAguAighQAggiAwABIMzAAQAvgBAiAiQAhAhAAAuQAAAughAiQgiAhgvABg");
  this.shape_21.setTransform(849.1,560);

  this.timeline.addTween(cjs.Tween.get({}).to({state:[{t:this.shape_21},{t:this.shape_20}]}).wait(25));

  // paper
  this.instance_1 = new lib_step4._1();
  this.instance_1.parent = this;
  this.instance_1.setTransform(402,19,0.235,0.199);

  this.timeline.addTween(cjs.Tween.get(this.instance_1).wait(25));

}).prototype = p_step4 = new cjs.MovieClip();
p_step4.nominalBounds = new cjs.Rectangle(1042,379,545.1,604.6);
// library properties:
lib_step4.properties = {
  id: '9D33D0B2ABC183428F26CA73B2961738_4',
  width: 1280,
  height: 720,
  fps: 24,
  color: "#FFFFFF",
  opacity: 1.00,
  manifest: [
    {src:"/bitrix/components/vportal/vivi_notify/templates/.default/images/notice1_cảnh4_đọc thông báo_atlas_.png?1533277850677", id:"notice1_cảnh4_đọc thông báo_atlas_"}
  ],
  preloads: []
};

// bootstrap callback support:
(lib_step4.Stage = function(canvas) {
  createjs.Stage.call(this, canvas);
}).prototype = p_step4 = new createjs.Stage();

p_step4.setAutoPlay = function(autoPlay) {
  this.tickEnabled = autoPlay;
}
p_step4.play = function() { this.tickEnabled = true; this.getChildAt(0).gotoAndPlay(this.getTimelinePosition()) }
p_step4.stop = function(ms) { if(ms) this.seek(ms); this.tickEnabled = false; }
p_step4.seek = function(ms) { this.tickEnabled = true; this.getChildAt(0).gotoAndStop(lib_step4.properties.fps * ms / 1000); }
p_step4.getDuration = function() { return this.getChildAt(0).totalFrames / lib_step4.properties.fps * 1000; }
p_step4.getTimelinePosition = function() { return this.getChildAt(0).currentFrame / lib_step4.properties.fps * 1000; }
//STEP4_END
//===============================================================================

//STEP4A_BEGIN
var p_step4a; // shortcut to reference prototypes
var lib_step4a={};var ss={};var img_step4a={};
lib_step4a.ssMetadata = [
    {name:"notice1_cảnh4a_đọc thông báo_atlas_", frames: [[0,0,2279,3031],[0,3033,297,302],[2500,500,273,265],[2500,0,512,500],[0,3033,297,302]]}
];

// symbols:
(lib_step4a._1 = function() {
  this.spriteSheet = ss["notice1_cảnh4a_đọc thông báo_atlas_"];
  this.gotoAndStop(0);
}).prototype = p_step4a = new cjs.Sprite();

(lib_step4a.happy1 = function() {
  this.spriteSheet = ss["notice1_cảnh4a_đọc thông báo_atlas_"];
  this.gotoAndStop(1);
}).prototype = p_step4a = new cjs.Sprite();

(lib_step4a.asleep = function() {
  this.spriteSheet = ss["notice1_cảnh4a_đọc thông báo_atlas_"];
  this.gotoAndStop(2);
}).prototype = p_step4a = new cjs.Sprite();

(lib_step4a.clock1 = function() {
  this.spriteSheet = ss["notice1_cảnh4a_đọc thông báo_atlas_"];
  this.gotoAndStop(3);
}).prototype = p_step4a = new cjs.Sprite();

(lib_step4a.happy2 = function() {
  this.spriteSheet = ss["notice1_cảnh4a_đọc thông báo_atlas_"];
  this.gotoAndStop(4);
}).prototype = p_step4a = new cjs.Sprite();

// stage content:
(lib_step4a.notice1_cảnh4a_đọcthôngbáo = function(mode,startPosition,loop) {
  this.initialize(mode,startPosition,true,{});

  // happy
  this.instance = new lib_step4a.happy1();
  this.instance.parent = this;
  this.instance.setTransform(815,393,0.445,0.445);
  this.instance._off = false;
  this.timeline.addTween(cjs.Tween.get(this.instance).wait(time.step4_delay)
    .to({_off:true},0));

  // asleep
  this.instance_1 = new lib_step4a.asleep();
  this.instance_1.parent = this;
  this.instance_1.setTransform(945,71,0.446,0.446);
  this.instance_1._off = false;

  this.timeline.addTween(cjs.Tween.get(this.instance_1)
    .to({_off:true},0).wait(time.step4_delay).to({_off:false},0).wait(15)
    .to({_off:true},0).wait(15).to({_off:false},0).wait(15)
    .to({_off:true},0).wait(15));

  // clock
  this.instance_2 = new lib_step4a.clock1();
  this.instance_2.parent = this;
  this.instance_2.setTransform(861,91,0.222,0.222);
  this.instance_2._off = false;

  this.timeline.addTween(cjs.Tween.get(this.instance_2)
    .to({_off:true},0).wait(time.step4_delay).to({_off:false},0).wait(15)
    .to({_off:true},0).wait(15).to({_off:false},0).wait(15)
    .to({_off:true},0).wait(15));

  // happy
  this.instance_3 = new lib_step4a.happy2();
  this.instance_3.parent = this;
  this.instance_3.setTransform(937,55,0.445,0.445);
  this.instance_3._off = false;
  this.timeline.addTween(cjs.Tween.get(this.instance_3)
    .to({_off:true},0).wait(time.step4_delay + 60)
    .to({_off:false},0).wait(90).to({_off:true},0));

  // text2
  this.shape22 = new cjs.Shape();
  this.shape22.graphics.f("#333333").s().p("AgEAkIAAgKIAJAAIAAAKgAgCASIgDglIAAgQIAKAAIAAAQIgCAlg");
  this.shape22.setTransform(933.5,112.8);

  this.shape_23 = new cjs.Shape();
  this.shape_23.graphics.f("#333333").s().p("AgEAjIAAgLIAJAAIAAALgAgSAPQgFgEAAgGQAAgEACgCQABgDADgCIAHgDIAIgCQAJgBAFgCIAAgBQAAgGgCgDQgEgCgGAAQgFAAgEACQgCACgCAGIgIgBQABgGADgDQACgEAFgCQAGgBAFAAQAHgBAEACQAEACACACQACACAAAEIABAIIAAAMIAAAOIADAHIgJAAIgCgHQgFAEgFACQgDABgFABQgJAAgEgFgAgBgDIgIABIgDADIgCADQAAAEADADQADACAFAAQAEAAAEgCQAEgCACgFIABgIIAAgCQgEACgJABg");
  this.shape_23.setTransform(929.3,114.5);

  this.shape_24 = new cjs.Shape();
  this.shape_24.graphics.f("#333333").s().p("AgRAUQgGgHAAgNQAAgNAIgIQAGgFAJAAQALAAAGAHQAHAHAAAMQAAAJgDAGQgDAGgGADQgFADgHAAQgKAAgHgHgAgKgOQgEAFAAAJQAAAKAEAFQAEAFAGAAQAGAAAFgFQAEgFAAgKQAAgJgEgFQgFgFgGAAQgGAAgEAFg");
  this.shape_24.setTransform(921,113.8);

  this.shape_25 = new cjs.Shape();
  this.shape_25.graphics.f("#333333").s().p("AgTAgQgEgEAAgGQAAgEACgDQABgDAEgCIAFgDIAIgCQAKgBAFgCIAAgBQAAgFgCgDQgEgCgGAAQgGAAgCACQgEACgBAFIgJAAQACgGADgDQACgEAFgCQAFgBAGAAQAHgBAEACQAEACACACQACACABAEIAAAHIAAAMIAAAPIADAHIgKAAIgBgHQgFAEgEACQgEABgFABQgIAAgGgFgAgBANIgIABIgEAEIAAADQAAAEACADQADACAFAAQAEAAAEgCQAEgCACgFIABgJIAAgCQgEACgJABgAgEgWIAGgNIALAAIgKANg");
  this.shape_25.setTransform(915.4,112.8);

  this.shape_26 = new cjs.Shape();
  this.shape_26.graphics.f("#333333").s().p("AgOAdIAAAGIgIAAIAAhGIAJAAIAAAZQAGgHAIAAQAEAAAFACQAEACACAEQAEADABAFQACAEgBAGQAAANgGAHQgHAHgJAAQgIAAgGgHgAgJgFQgEAFgBAJQABAJACAEQAEAHAHAAQAFAAAFgFQADgFAAgKQAAgJgDgFQgFgFgFAAQgFAAgEAFg");
  this.shape_26.setTransform(910,112.8);

  this.shape_27 = new cjs.Shape();
  this.shape_27.graphics.f("#333333").s().p("AgOAhQgGgFAAgHIAIABQABADACADQAEACAFAAQAGAAADgCQADgDACgEIAAgMQgGAHgIAAQgKAAgGgIQgGgHAAgKQAAgIADgGQADgGAEgDQAGgEAGAAQAJAAAGAHIAAgGIAIAAIAAAsQAAAMgCAFQgDAFgFADQgGADgHAAQgJAAgFgEgAgJgYQgEAFABAJQgBAKAEAEQAFAFAFgBQAFABAFgFQADgEAAgKQAAgJgDgFQgFgFgFAAQgFAAgFAFg");
  this.shape_27.setTransform(901.4,114.8);

  this.shape_28 = new cjs.Shape();
  this.shape_28.graphics.f("#333333").s().p("AANAbIAAgfIgBgIIgEgEQgDgCgDAAQgFAAgEADQgEAEAAAKIAAAcIgJAAIAAgzIAIAAIAAAHQAFgIALAAQAEgBAEACQAEACACADQACADABADIAAAIIAAAgg");
  this.shape_28.setTransform(896,113.7);

  this.shape_29 = new cjs.Shape();
  this.shape_29.graphics.f("#333333").s().p("AgRAdQgGgGAAgNQAAgOAIgHQAGgFAJAAQALgBAGAIQAHAGAAAMQAAAKgDAGQgDAFgGAEQgFADgHAAQgKAAgHgIgAgKgFQgEAFAAAKQAAAJAEAGQAEAEAGAAQAGAAAFgEQAEgGAAgJQAAgKgEgFQgFgEgGAAQgGAAgEAEgAAFgWIgFgIIgFAIIgKAAIALgNIAIAAIALANg");
  this.shape_29.setTransform(890.4,112.8);

  this.shape_30 = new cjs.Shape();
  this.shape_30.graphics.f("#333333").s().p("AAMAkIAAghQAAgGgDgDQgCgDgGAAQgDAAgDACQgDACgCAEQgBACAAAGIAAAdIgJAAIAAhHIAJAAIAAAaQAGgHAIAAQAGAAAEACQAEACACAEQACAEAAAHIAAAhg");
  this.shape_30.setTransform(884.9,112.8);

  this.shape_31 = new cjs.Shape();
  this.shape_31.graphics.f("#333333").s().p("AgBAiQgCgCgBgCQgCgDABgHIAAgdIgHAAIAAgHIAHAAIAAgNIAIgFIAAASIAJAAIAAAHIgJAAIAAAdIAAAFIABACIADAAIAFAAIABAIIgHAAQgFAAgCgBg");
  this.shape_31.setTransform(880.8,112.9);

  this.shape_32 = new cjs.Shape();
  this.shape_32.graphics.f("#333333").s().p("AgPAUQgHgGABgOQgBgHADgGQADgHAGgDQAFgDAGAAQAIAAAGAFQAFAEACAHIgJABQgBgFgDgCQgDgDgFAAQgFAAgFAFQgDAFAAAJQAAAKADAFQAFAFAFAAQAFAAADgDQAEgDABgHIAJABQgCAJgFAFQgHAFgIAAQgKAAgGgHg");
  this.shape_32.setTransform(874.2,113.8);

  this.shape_33 = new cjs.Shape();
  this.shape_33.graphics.f("#333333").s().p("AgEAjIAAgLIAJAAIAAALgAgRAMQgGgGAAgMQAAgPAIgHQAGgFAJAAQALgBAGAIQAHAGAAANQAAAJgDAGQgDAFgGAEQgFADgHAAQgKAAgHgIgAgKgWQgEAGAAAKQAAAIAEAGQAEAEAGAAQAGAAAFgEQAEgGAAgIQAAgKgEgGQgFgEgGAAQgGAAgEAEg");
  this.shape_33.setTransform(868.7,114.5);

  this.shape_34 = new cjs.Shape();
  this.shape_34.graphics.f("#333333").s().p("AgOAhQgGgDgCgHQgDgGAAgIQAAgHADgGQACgGAGgDQAFgEAGAAQAEAAADACQAEACACADIAAgNIgPAAIAAgHIAPAAIAAgFIAJAAIAAAFIAHAAIAAAHIgHAAIAAA6IgJAAIAAgGQgEAHgJAAQgGAAgFgDgAgMgFQgEAFAAAJQAAAKAEAFQAFAFAFAAQAFAAAEgEQAEgFAAgKQAAgKgEgFQgFgFgEAAQgGAAgEAFg");
  this.shape_34.setTransform(863.4,112.8);

  this.shape_35 = new cjs.Shape();
  this.shape_35.graphics.f("#333333").s().p("AgEAsIAAgKIAJAAIAAAKgAgDAcIAAgzIAIAAIAAAzgAgDghIAAgKIAIAAIAAAKg");
  this.shape_35.setTransform(856.5,113.6);

  this.shape_35 = new cjs.Shape();
  this.shape_35.graphics.f("#333333").s().p("AAMAkIAAghQAAgGgDgDQgCgDgGAAQgDAAgDACQgDACgCAEQgBACAAAGIAAAdIgJAAIAAhHIAJAAIAAAaQAGgHAIAAQAGAAAEACQAEACACAEQACAEAAAHIAAAhg");
  this.shape_35.setTransform(852.6,112.8);

  this.shape_36 = new cjs.Shape();
  this.shape_36.graphics.f("#333333").s().p("AgPAUQgHgGABgOQAAgHACgGQADgHAGgDQAFgDAGAAQAIAAAGAFQAFAEACAHIgJABQgBgFgDgCQgDgDgFAAQgGAAgEAFQgDAFAAAJQAAAKADAFQAEAFAGAAQAFAAADgDQAEgDABgHIAJABQgCAJgFAFQgHAFgIAAQgKAAgGgHg");
  this.shape_36.setTransform(847.5,113.8);

  this.shape_37 = new cjs.Shape();
  this.shape_37.graphics.f("#333333").s().p("AgNAlIAUhJIAHAAIgUBJg");
  this.shape_37.setTransform(843.5,112.8);

  this.shape_38 = new cjs.Shape();
  this.shape_38.graphics.f("#333333").s().p("AAMAkIAAghQAAgGgDgDQgCgDgGAAQgDAAgDACQgDACgCAEQgBACAAAGIAAAdIgJAAIAAhHIAJAAIAAAaQAGgHAIAAQAGAAAEACQAEACACAEQACAEAAAHIAAAhg");
  this.shape_38.setTransform(839.3,112.8);

  this.shape_39 = new cjs.Shape();
  this.shape_39.graphics.f("#333333").s().p("AAMAbIAAgfIgBgIIgDgEQgDgCgEAAQgEAAgEADQgEAEAAAKIAAAcIgJAAIAAgzIAIAAIAAAHQAFgIAKAAQAFgBAEACQAEACACADQACADAAADIABAIIAAAgg");
  this.shape_39.setTransform(833.7,113.7);

  this.shape_40 = new cjs.Shape();
  this.shape_40.graphics.f("#333333").s().p("AgSAXQgFgEAAgGQAAgFACgDQABgDADgCIAHgCIAIgBQAJgBAFgCIAAgCQAAgGgCgCQgDgDgHAAQgFAAgEACQgDADgBAFIgIgBQABgGACgDQADgEAFgBQAFgCAGAAQAHAAAEACQAEABACACQACADAAAEIABAIIAAALIABAPIACAGIgJAAIgCgGQgFAEgEABQgEACgFAAQgJAAgEgEgAgBAEIgIABIgDADIgBAEQAAADACADQADADAFAAQAEAAAEgDQAEgCACgEIABgJIAAgDQgEACgJACg");
  this.shape_40.setTransform(828.2,113.8);

  this.shape_41 = new cjs.Shape();
  this.shape_41.graphics.f("#333333").s().p("AgDAkIAAgzIAIAAIAAAzgAgDgZIAAgKIAIAAIAAAKg");
  this.shape_41.setTransform(821.5,112.8);

  this.shape_42 = new cjs.Shape();
  this.shape_42.graphics.f("#333333").s().p("AgWAdQgHgGABgNQAAgOAIgHQAGgFAJAAQAKgBAHAIQAFAFABAHIAGgDIABgHIgFAAIAAgJIAJAAIAAAIQAAAGgCACQgCAEgGAEIAAABQAAASgMAHQgFADgHAAQgLAAgGgIgAgQgFQgDAFAAAKQAAAJADAGQAFAEAGAAQAGAAAFgEQAEgGAAgJQAAgKgEgFQgFgEgGAAQgGAAgFAEgAgGgWIgLgNIAMAAIAGANg");
  this.shape_42.setTransform(817.2,112.8);

  this.shape_43 = new cjs.Shape();
  this.shape_43.graphics.f("#333333").s().p("AAZAkIAAg7IgVA7IgHAAIgVg8IAAA8IgJAAIAAhHIAOAAIARAyIADAKIADgLIARgxIANAAIAABHg");
  this.shape_43.setTransform(809.7,112.8);

  this.timeline.addTween(cjs.Tween.get({})
    .to({state:[]}).wait(time.step4_delay + 60)
    .to({state:[{t:this.shape_43},{t:this.shape_42},
      {t:this.shape_41},{t:this.shape_40},{t:this.shape_39},
      {t:this.shape_38},{t:this.shape_37},{t:this.shape_36},
      {t:this.shape_35},{t:this.shape_34},{t:this.shape_33},
      {t:this.shape_32},{t:this.shape_31},{t:this.shape_30},
      {t:this.shape_29},{t:this.shape_28},{t:this.shape_27},
      {t:this.shape_26},{t:this.shape_25},{t:this.shape_24},
      {t:this.shape_23},{t:this.shape22}]}).wait(90)
    .to({state:[]}));

  // notice_box
  this.shape_44 = new cjs.Shape();
  this.shape_44.graphics.f().s("#999999").ss(1,1,1).p("AOKAAQAACwkJB9QkKB9l3AAQl3AAkJh9QkJh9AAiwQAAivEJh9QEJh9F3AAQF3AAEKB9QEJB9AACvg");
  this.shape_44.setTransform(871.6,112.6);

  this.shape_45 = new cjs.Shape();
  this.shape_45.graphics.f("#FFFFFF").s().p("AqAEuQkJh+gBiwQABiwEJh8QEKh+F2ABQF3gBEKB+QEJB8ABCwQgBCwkJB+QkKB8l3AAQl2AAkKh8g");
  this.shape_45.setTransform(871.6,112.6);

  this.timeline.addTween(cjs.Tween.get({})
    .to({state:[]}).wait(time.step4_delay + 60)
    .to({state:[{t:this.shape_45},{t:this.shape_44}]}).wait(90)
    .to({state:[]}));

  // text
  this.shape = new cjs.Shape();
  this.shape.graphics.f("#FFFFFF").s().p("AgUAYQgIgJAAgPQAAgOAIgJQAIgIANgBQALAAAHAGQAHAEADAKIgRADQAAgFgDgCQgDgDgFAAQgFABgEAEQgDAEAAAKQAAAKADAEQAEAFAFAAQAFAAADgDQADgDABgGIAQADQgCALgHAGQgHAFgMAAQgNAAgIgIg");
  this.shape.setTransform(894.2,561.3);

  this.shape_1 = new cjs.Shape();
  this.shape_1.graphics.f("#FFFFFF").s().p("AgIAsIAAgRIAPAAIAAARgAgQASQgHgFgEgHQgEgHAAgKQAAgIAEgIQAEgHAHgFQAIgEAIAAQAOAAAJAJQAJAJAAAPQAAANgJAJQgJAJgOAAQgHAAgJgDgAgKgZQgFAFAAAKQAAAJAFAEQAFAFAFAAQAHAAAEgFQAEgEAAgJQAAgKgEgFQgEgEgHAAQgFAAgFAEg");
  this.shape_1.setTransform(887.1,562.4);

  this.shape_2 = new cjs.Shape();
  this.shape_2.graphics.f("#FFFFFF").s().p("AgZAjQgIgJAAgPQAAgPAIgIQAIgIAKAAQAKAAAHAIIAAgOIgQAAIAAgLIAQAAIAAgGIARAAIAAAGIAHAAIAAALIgHAAIAABEIgPAAIAAgJQgEAFgFADQgEADgGAAQgKAAgIgJgAgMgDQgEAEAAAJQAAAKADAEQAEAHAHAAQAFAAAEgFQAEgFAAgKQAAgKgEgEQgEgFgFAAQgGAAgEAFg");
  this.shape_2.setTransform(879.9,560.1);

  this.shape_3 = new cjs.Shape();
  this.shape_3.graphics.f("#FFFFFF").s().p("AgWAmQgGgFAAgIQAAgGADgEQACgDAEgDQAFgCAJgBQAKgCAFgCIAAgCQAAgEgDgCQgCgCgGAAQgEAAgCACQgDABgCAFIgPgDQADgJAGgFQAHgEALABQAKAAAGACQAFADACADQACAEAAALIAAATIABALIADAJIgQAAIgBgFIgBgCQgFAEgEACQgEACgFAAQgKAAgFgFgAAAAOQgGACgDABQgDADAAAEQAAADADADQADACADAAQAEAAAEgDQADgDABgCIABgJIAAgDIgKACgAgTgcIgBgCQAAgGAEgDQADgDAEAAIAEAAIAGADIAHABIADAAQAAgBABAAQAAgBAAAAQAAgBAAAAQABgBAAAAIAIAAQgBAHgDAEQgDADgEAAIgEAAIgGgCIgHgCIgEABIgBADg");
  this.shape_3.setTransform(869.4,560.2);

  this.shape_4 = new cjs.Shape();
  this.shape_4.graphics.f("#FFFFFF").s().p("AgZAjQgIgJABgPQgBgPAIgIQAIgIALAAQAJAAAIAIIAAgOIgRAAIAAgLIARAAIAAgGIAQAAIAAAGIAGAAIAAALIgGAAIAABEIgPAAIAAgJQgEAFgFADQgEADgFAAQgLAAgIgJgAgMgDQgEAEAAAJQAAAKADAEQAEAHAHAAQAFAAAEgFQAEgFAAgKQAAgKgEgEQgEgFgFAAQgGAAgEAFg");
  this.shape_4.setTransform(862.6,560.1);

  this.shape_5 = new cjs.Shape();
  this.shape_5.graphics.f("#FFFFFF").s().p("AgSAdQgFgDgCgFQgCgFAAgJIAAgmIAQAAIAAAdQAAAMABADQABADACABQADACADAAQAEAAADgCQADgDABgDQACgDAAgNIAAgaIAQAAIAAA9IgPAAIAAgJQgEAFgFADQgFADgGAAQgGAAgFgDg");
  this.shape_5.setTransform(851.7,561.3);

  this.shape_6 = new cjs.Shape();
  this.shape_6.graphics.f("#FFFFFF").s().p("AgXAxQgFgFAAgIQAAgGACgEQADgEAFgCQAEgCAIgCQALgCAEgCIAAgBQAAgFgCgCQgCgCgFAAQgFAAgDABQgCACgBAFIgPgDQACgIAGgFQAHgEALAAQALAAAFADQAFACADAEQACADAAALIAAATIAAAMIADAJIgQAAIgCgFIgBgCQgDAEgGACQgDACgGAAQgJAAgGgFgAAAAZQgHACgCABQgDADAAADQAAAEADACQADADAEAAQADAAAEgDQADgDABgDIAAgIIAAgDIgJACgAAHgQIgHgJIgGAJIgNAAIAMgSIAPAAIAMASgAgHglIAHgQIASAAIgQAQg");
  this.shape_6.setTransform(844.7,559.1);

  this.shape_7 = new cjs.Shape();
  this.shape_7.graphics.f("#FFFFFF").s().p("AgWAjQgHgJAAgPQAAgPAHgIQAIgIALAAQAJAAAIAIIAAgfIAQAAIAABVIgPAAIAAgJQgEAFgFADQgFADgEAAQgLAAgIgJgAgJgDQgEAEAAAJQAAAKADAEQAEAHAGAAQAGAAAEgFQAEgFAAgKQAAgKgEgEQgEgFgGAAQgFAAgEAFg");
  this.shape_7.setTransform(837.5,560.1);

  this.shape_8 = new cjs.Shape();
  this.shape_8.graphics.f("#FFFFFF").s().p("AAMArIAAghQAAgKgBgCQgBgCgCgBQgDgCgDAAQgDAAgEACQgDACgBADQgCAEAAAIIAAAfIgQAAIAAhVIAQAAIAAAfQAIgJAKABQAGAAAFABQAEACACAEQADADABAEIAAAMIAAAkg");
  this.shape_8.setTransform(827,560.1);

  this.shape_9 = new cjs.Shape();
  this.shape_9.graphics.f("#FFFFFF").s().p("AAMAgIAAggQAAgJgBgDQgBgDgCgCQgDgBgDAAQgDAAgEACQgDACgBAEQgCAEAAAKIAAAcIgQAAIAAg9IAPAAIAAAJQAIgLAMAAQAFAAAFACQAEACACADQADADABAEIAAALIAAAmg");
  this.shape_9.setTransform(819.6,561.2);

  this.shape_10 = new cjs.Shape();
  this.shape_10.graphics.f("#FFFFFF").s().p("AgXAnQgFgFAAgIQAAgGADgEQACgDAFgDQAEgCAIgBQALgCAEgCIAAgCQAAgEgCgCQgCgCgFAAQgFAAgDACQgCABgBAEIgPgCQACgJAGgFQAHgEALABQALAAAFACQAFADADADQACAEAAALIAAATIABALIACAJIgQAAIgCgFIgBgCQgEAEgFACQgDACgGAAQgJAAgGgFgAAAAPQgHACgBABQgEADAAAEQAAADADADQACACAFAAQADAAAEgDQADgDABgCIAAgJIAAgDIgJACgAgIgaIAIgSIASAAIgQASg");
  this.shape_10.setTransform(812.7,560.1);

  this.shape_11 = new cjs.Shape();
  this.shape_11.graphics.f("#FFFFFF").s().p("AgfArIAAglIgIAAIAAgKIAIAAIAAgmIAfAAQALAAAGACQAHACAGAGQAFAFADAJQACAIAAALQAAALgCAJQgEAIgFAFQgFAEgIADQgFACgKAAgAgNAcIANAAQAHAAAEgBQAFgBADgFQAEgHAAgOQAAgIgCgFQgCgGgDgDQgDgDgEgCIgOgBIgIAAIAAAYIARAAIAAAKIgRAAg");
  this.shape_11.setTransform(804.7,560.1);

  this.shape_12 = new cjs.Shape();
  this.shape_12.graphics.f("#666666").s().p("Ag+BwQgcgRgOgdQgPgeAAghQAAg9AigjQAhgkA0AAQAjAAAcARQAcAQAOAeQAPAeAAAkQAAAmgQAfQgPAegcAPQgcAQghAAQgiAAgcgSgAg7hMQgaAYAAA4QAAAtAZAaQAYAaAkAAQAlAAAYgaQAZgaAAgxQAAgdgLgXQgKgXgUgNQgUgMgZAAQgiAAgZAYg");
  this.shape_12.setTransform(784.3,94.3);

  this.shape_13 = new cjs.Shape();
  this.shape_13.graphics.f("#666666").s().p("ABPCdIgdhMIhoAAIgbBMIgjAAIBgj6IAjAAIBmD6gAgQgSIgcBIIBUAAIgahFQgMgfgFgUQgFAYgIAYgAgXhsIAWgwIAoAAIgmAwg");
  this.shape_13.setTransform(758.9,91.1);

  this.shape_14 = new cjs.Shape();
  this.shape_14.graphics.f("#666666").s().p("AheB9IAAj5IBeAAQAcAAASAHQARAIAKAQQAJAQAAARQAAAQgIAOQgJAOgSAJQAXAHAMAPQANAQAAAWQAAARgIAPQgHAPgLAJQgLAIgRAEQgQAEgYAAgAg8BgIA9AAQAQAAAGgCQAMgCAIgEQAHgFAFgJQAFgJAAgMQAAgOgHgLQgHgKgNgEQgNgEgXAAIg5AAgAg8gTIA2AAQAVAAAJgDQANgEAGgIQAHgJAAgNQAAgNgGgJQgGgKgLgDQgLgEgaAAIgyAAg");
  this.shape_14.setTransform(735.9,94.3);

  this.shape_15 = new cjs.Shape();
  this.shape_15.graphics.f("#666666").s().p("Ag2ByQgegQgQgeQgPgeAAglQAAgjAPggQAPggAdgPQAdgQAkAAQAcAAAWAJQAWAJAMAQQANAPAGAaIgeAIQgGgTgIgLQgIgLgQgHQgPgGgUAAQgVAAgRAHQgQAHgKALQgLALgFAOQgKAXAAAbQAAAhAMAWQAMAXAVALQAXALAXAAQAWAAAUgJQAVgIALgJIAAgvIhLAAIAAgcIBrgBIAABcQgZAUgZAKQgaAKgcAAQgkAAgegQg");
  this.shape_15.setTransform(700.5,94.3);

  this.shape_16 = new cjs.Shape();
  this.shape_16.graphics.f("#666666").s().p("ABBB9IiDjEIAADEIggAAIAAj5IAiAAICDDDIAAjDIAgAAIAAD5g");
  this.shape_16.setTransform(674.3,94.3);

  this.shape_17 = new cjs.Shape();
  this.shape_17.graphics.f("#666666").s().p("Ag+COQgcgRgOgeQgPgdAAghQAAg+AigjQAhgjA0AAQAjAAAcAQQAcARAOAeQAPAdAAAlQAAAmgQAeQgPAegcAQQgcAPghAAQgiAAgcgRgAg7guQgaAYAAA3QAAAuAZAaQAYAaAkAAQAlAAAYgbQAZgaAAgwQAAgegLgXQgKgXgUgMQgUgNgZAAQgiAAgZAZgAAShuIgSgdIgTAdIgjAAIAlgwIAgAAIAmAwg");
  this.shape_17.setTransform(648.2,91.3);

  this.shape_18 = new cjs.Shape();
  this.shape_18.graphics.f("#666666").s().p("ABBB9IAAh2IiBAAIAAB2IghAAIAAj5IAhAAIAABnICBAAIAAhnIAhAAIAAD5g");
  this.shape_18.setTransform(621.9,94.3);

  this.shape_19 = new cjs.Shape();
  this.shape_19.graphics.f("#666666").s().p("AgQB9IAAjcIhTAAIAAgdIDGAAIAAAdIhTAAIAADcg");
  this.shape_19.setTransform(598.7,94.3);

  this.timeline.addTween(cjs.Tween.get({}).to({state:[{t:this.shape_19},{t:this.shape_18},{t:this.shape_17},{t:this.shape_16},{t:this.shape_15},{t:this.shape_14},{t:this.shape_13},{t:this.shape_12},{t:this.shape_11},{t:this.shape_10},{t:this.shape_9},{t:this.shape_8},{t:this.shape_7},{t:this.shape_6},{t:this.shape_5},{t:this.shape_4},{t:this.shape_3},{t:this.shape_2},{t:this.shape_1},{t:this.shape}]}).wait(25));

  // button
  //vien do
  this.shape_20 = new cjs.Shape();
  this.shape_20.graphics.f().s("#FF0000").ss(1,1,1).p("AmZhxIM0AAQAuAAAiAiQAhAhAAAuQAAAughAiQgiAhguAAIs0AAQgwAAggghQgigiAAguQAAguAighQAggiAwAAg");
  this.shape_20.setTransform(849.1,560);
  //nen button
  this.shape_21 = new cjs.Shape();
  this.shape_21.graphics.f("#FF6633").s().p("AmZByQgwgBggghQgigiAAguQAAguAighQAggiAwABIMzAAQAvgBAiAiQAhAhAAAuQAAAughAiQgiAhgvABg");
  this.shape_21.setTransform(849.1,560);

  this.timeline.addTween(
    cjs.Tween.get({})
    .to({state:[{t:this.shape_21},{t:this.shape_20}]}).wait(25));

  // paper
  this.instance_1 = new lib_step4a._1();
  this.instance_1.parent = this;
  this.instance_1.setTransform(402,19,0.235,0.199);

  this.timeline.addTween(cjs.Tween.get(this.instance_1).wait(25));

}).prototype = p_step4a = new cjs.MovieClip();
p_step4a.nominalBounds = new cjs.Rectangle(1042,379,545.1,604.6);
// library properties:
lib_step4a.properties = {
  id: '9D33D0B2ABC183428F26CA73B2961738_4a',
  width: 1280,
  height: 720,
  fps: 24,
  color: "#FFFFFF",
  opacity: 1.00,
  manifest: [
    {src:"/bitrix/components/vportal/vivi_notify/templates/.default/images/notice1_cảnh4a_đọc thông báo_atlas_.png?1533277850677", id:"notice1_cảnh4a_đọc thông báo_atlas_"}
  ],
  preloads: []
};

// bootstrap callback support:
(lib_step4a.Stage = function(canvas) {
  createjs.Stage.call(this, canvas);
}).prototype = p_step4a = new createjs.Stage();

p_step4a.setAutoPlay = function(autoPlay) {
  this.tickEnabled = autoPlay;
}
p_step4a.play = function() { this.tickEnabled = true; this.getChildAt(0).gotoAndPlay(this.getTimelinePosition()) }
p_step4a.stop = function(ms) { if(ms) this.seek(ms); this.tickEnabled = false; }
p_step4a.seek = function(ms) { this.tickEnabled = true; this.getChildAt(0).gotoAndStop(lib_step4a.properties.fps * ms / 1000); }
p_step4a.getDuration = function() { return this.getChildAt(0).totalFrames / lib_step4a.properties.fps * 1000; }

p_step4a.getTimelinePosition = function() { return this.getChildAt(0).currentFrame / lib_step4a.properties.fps * 1000; }
//STEP4A_END
//================================================================

//STEP5_BEGIN
var p_step5; // shortcut to reference prototypes
var lib_step5={};var ss_step5={};var img_step5={};
lib_step5.ssMetadata = [];

// symbols:
(lib_step5.happy = function() {
  this.initialize(img_step5.happy);
}).prototype = p_step5 = new cjs.Bitmap();
p_step5.nominalBounds = new cjs.Rectangle(0,0,297,302);

// stage content:
(lib_step5.notice1_cảnh5_tạmbiệt = function(mode,startPosition,loop) {
  this.initialize(mode,startPosition,loop,{});

  // happy
  this.instance = new lib_step5.happy();
  this.instance.parent = this;
  this.instance.setTransform(937,55,0.445,0.445);

  this.timeline.addTween(cjs.Tween.get(this.instance).wait(19));

  // text
  this.shape = new cjs.Shape();
  this.shape.graphics.f("#333333").s().p("AgEAkIAAgKIAJAAIAAAKgAgBASIgDglIAAgQIAKAAIAAAQIgDAlg");
  this.shape.setTransform(950.6,115.5);

  this.shape_1 = new cjs.Shape();
  this.shape_1.graphics.f("#333333").s().p("AgTAiQgEgEAAgGQAAgEACgDQABgEAEgBIAFgDIAIgCQAKgBAFgCIAAgCQAAgEgCgDQgDgDgHAAQgFAAgEADQgCACgCAEIgJgBQACgEACgEQADgDAFgCQAGgCAFAAQAHAAAEACQAEABACADQACACABAEIAAAHIAAAMIABAPIACAGIgKAAIgBgGQgFAEgEACQgEABgFAAQgIAAgGgEgAgBAPIgIABIgEADIAAAEQgBAEADACQADADAFAAQAEAAAEgDQAEgCACgEIABgJIAAgDQgFACgIACgAgBgUIAAgGIADAAQABAAAAgBQABAAAAgBQAAAAAAAAQAAgBAAAAIgBgCIgGgBIgEAAIAAgFIAGAAQAGAAADACQAFACAAADQAAAGgJACIAAACg");
  this.shape_1.setTransform(946.4,115.4);

  this.shape_2 = new cjs.Shape();
  this.shape_2.graphics.f("#333333").s().p("AgLAZQgEgCgCgCQgCgDAAgEIgBgIIAAgfIAIAAIAAAcIABAJQABADADACQACACAFAAQADAAADgCQADgCABgDQACgEAAgGIAAgbIAJAAIAAAyIgIAAIAAgHQgGAJgKgBQgEAAgEgBg");
  this.shape_2.setTransform(940.8,116.6);

  this.shape_3 = new cjs.Shape();
  this.shape_3.graphics.f("#333333").s().p("AAOAlIAAgaQgDADgDACIgIABQgIAAgHgGQgGgIgBgMQAAgIADgGQADgGAFgDQAFgDAGgBQAJABAFAHIAAgGIAIAAIAABHgAgJgXQgEAEAAAKQAAAKAEAEQAFAFAFABQAFgBAFgFQADgEAAgJQAAgKgDgFQgFgGgGAAQgEAAgFAGg");
  this.shape_3.setTransform(935.1,117.5);

  this.shape_4 = new cjs.Shape();
  this.shape_4.graphics.f("#333333").s().p("AgLAZQgEgCgCgCQgCgDgBgEIAAgIIAAgfIAIAAIAAAcIABAJQABADACACQAEACADAAQADAAAEgCQADgCACgDQABgEAAgGIAAgbIAJAAIAAAyIgIAAIAAgHQgGAJgKgBQgEAAgEgBg");
  this.shape_4.setTransform(926.9,116.6);

  this.shape_5 = new cjs.Shape();
  this.shape_5.graphics.f("#333333").s().p("AgEAsIAAgKIAKAAIAAAKgAgQAWQgHgHAAgNQAAgLAHgIQAHgHAKAAQAKAAAHAHQAGAHAAAMIAAACIgmAAQABAJAEAFQAEAEAGAAQAFAAADgCQADgDACgGIAJABQgBAJgHAEQgFAEgJAAQgLAAgGgHgAgIgNQgFAFgBAGIAcAAQAAgGgDgEQgEgFgGAAQgGAAgDAEgAAFgeIgFgIIgFAIIgKAAIALgNIAIAAIALANg");
  this.shape_5.setTransform(921.4,116.3);

  this.shape_6 = new cjs.Shape();
  this.shape_6.graphics.f("#333333").s().p("AgEAkIAAgzIAIAAIAAAzgAgEgZIAAgKIAIAAIAAAKg");
  this.shape_6.setTransform(917.5,115.5);

  this.shape_7 = new cjs.Shape();
  this.shape_7.graphics.f("#333333").s().p("AAMAkIAAghQAAgGgDgDQgCgDgGAAQgDAAgDACQgDACgCAEQgBACAAAGIAAAdIgJAAIAAhHIAJAAIAAAaQAGgHAIAAQAGAAAEACQAEACACAEQACAEAAAHIAAAhg");
  this.shape_7.setTransform(913.6,115.5);

  this.shape_8 = new cjs.Shape();
  this.shape_8.graphics.f("#333333").s().p("AgPAUQgHgHAAgNQAAgHADgHQADgFAGgEQAGgDAFAAQAJAAAFAEQAFAFABAIIgIABQgBgFgDgDQgDgDgFAAQgGAAgDAFQgFAFAAAJQAAALAFAEQAEAFAFAAQAFAAAEgDQADgDABgHIAIABQgBAJgFAFQgHAFgIAAQgJAAgHgHg");
  this.shape_8.setTransform(905.7,116.5);

  this.shape_9 = new cjs.Shape();
  this.shape_9.graphics.f("#333333").s().p("AgDAsIAAgKIAJAAIAAAKgAgQAWQgHgHAAgNQAAgLAHgIQAGgHAKAAQALAAAGAHQAHAHAAAMIAAACIgmAAQAAAJAFAFQAEAEAGAAQAFAAADgCQAEgDACgGIAJABQgDAJgGAEQgFAEgJAAQgLAAgGgHgAgJgNQgEAFAAAGIAcAAQgBgGgCgEQgFgFgHAAQgFAAgEAEgAAGgeIgGgIIgEAIIgKAAIAKgNIAJAAIAKANg");
  this.shape_9.setTransform(900.3,116.3);

  this.shape_10 = new cjs.Shape();
  this.shape_10.graphics.f("#333333").s().p("AgDAkIAAgzIAHAAIAAAzgAgDgZIAAgKIAHAAIAAAKg");
  this.shape_10.setTransform(896.4,115.5);

  this.shape_11 = new cjs.Shape();
  this.shape_11.graphics.f("#333333").s().p("AgDAaIgUgzIAJAAIALAeIADALIADgKIAMgfIAJAAIgUAzg");
  this.shape_11.setTransform(892.8,116.5);

  this.shape_12 = new cjs.Shape();
  this.shape_12.graphics.f("#333333").s().p("AAaAaIAAgfIAAgHQgBgDgDgCQgCgBgDAAQgFAAgEAEQgEADAAAIIAAAdIgHAAIAAggQAAgGgCgDQgDgDgFAAQgDAAgDACQgEACgBAEQgBAEAAAGIAAAaIgJAAIAAgyIAIAAIAAAHQACgEAEgCQAEgDAFAAQAGAAAEADQADACABAEQAGgIAKgBQAIAAAEAFQAEAEAAAJIAAAig");
  this.shape_12.setTransform(883.3,116.5);

  this.shape_13 = new cjs.Shape();
  this.shape_13.graphics.f("#333333").s().p("AgSAgQgFgDAAgHQAAgEACgDQACgEADgBIAFgDIAIgCQAKAAAFgCIAAgDQAAgEgCgCQgEgEgGAAQgGABgCACQgEACgBAFIgJgBQACgFADgEQACgDAFgBQAFgCAGgBQAGAAAFACQAEABACADQACADAAADIABAHIAAAMIAAAQIADAFIgKAAIgBgGQgFAEgFACQgDABgFAAQgJAAgEgEgAgBANIgIABIgEADIgBAFQABADACACQADADAFAAQAEAAAEgDQAEgCACgDIABgJIAAgEQgFADgIABgAABgWIgLgOIALAAIAHAOg");
  this.shape_13.setTransform(876.4,115.6);

  this.shape_14 = new cjs.Shape();
  this.shape_14.graphics.f("#333333").s().p("AgDAkIAAhHIAHAAIAABHg");
  this.shape_14.setTransform(872.5,115.5);

  this.shape_15 = new cjs.Shape();
  this.shape_15.graphics.f("#333333").s().p("AgSAkIgBgJIAFABIAFgBIADgDIACgGIABgDIgUgyIAKAAIALAeIACAKIAEgKIALgeIAJAAIgUAzIgEAMQgCAFgDABQgDADgEAAIgGgBg");
  this.shape_15.setTransform(866.1,117.6);

  this.shape_16 = new cjs.Shape();
  this.shape_16.graphics.f("#333333").s().p("AgSAgQgFgDAAgHQAAgEACgDQACgEACgBIAHgDIAIgCQAJAAAFgCIAAgDQAAgEgCgCQgEgEgGAAQgGABgCACQgDACgCAFIgJgBQACgFADgEQACgDAFgBQAGgCAFgBQAGAAAFACQAEABACADQACADAAADIABAHIAAAMIAAAQIADAFIgJAAIgCgGQgFAEgFACQgDABgFAAQgJAAgEgEgAgBANIgIABIgEADIgBAFQAAADADACQADADAFAAQAEAAAEgDQAEgCACgDIABgJIAAgEQgFADgIABgAAAgWIgKgOIALAAIAHAOg");
  this.shape_16.setTransform(860.8,115.6);

  this.shape_17 = new cjs.Shape();
  this.shape_17.graphics.f("#333333").s().p("AgOAhQgGgEAAgJIAIABQABAFADABQADADAFAAQAGAAADgDQADgCABgFIABgLQgGAHgIAAQgKAAgGgIQgGgIAAgJQAAgIADgGQADgGAEgEQAGgDAGAAQAJAAAGAIIAAgHIAIAAIAAAsQAAAMgCAFQgDAFgGADQgEADgIAAQgJAAgFgEgAgIgYQgFAFAAAJQAAAKAFAEQADAEAGAAQAFAAAFgEQADgEAAgJQAAgKgDgFQgFgFgFAAQgGAAgDAFg");
  this.shape_17.setTransform(855.1,117.5);

  this.shape_18 = new cjs.Shape();
  this.shape_18.graphics.f("#333333").s().p("AANAaIAAgeIgBgIIgEgFQgDgBgDAAQgFAAgEAEQgEADAAAKIAAAbIgJAAIAAgyIAIAAIAAAHQAFgJAKAAQAFAAAEACQAEACACACQACADABAEIAAAJIAAAeg");
  this.shape_18.setTransform(849.7,116.5);

  this.shape_19 = new cjs.Shape();
  this.shape_19.graphics.f("#333333").s().p("AgEAsIAAgKIAJAAIAAAKgAgEAcIAAgzIAIAAIAAAzgAgEghIAAgKIAIAAIAAAKg");
  this.shape_19.setTransform(843.1,116.3);

  this.shape_20 = new cjs.Shape();
  this.shape_20.graphics.f("#333333").s().p("AAMAkIAAghQAAgGgDgDQgCgDgGAAQgDAAgDACQgDACgCAEQgBACAAAGIAAAdIgJAAIAAhHIAJAAIAAAaQAGgHAIAAQAGAAAEACQAEACACAEQACAEAAAHIAAAhg");
  this.shape_20.setTransform(839.2,115.5);

  this.shape_21 = new cjs.Shape();
  this.shape_21.graphics.f("#333333").s().p("AgPAUQgGgHgBgNQABgHACgHQADgFAFgEQAHgDAFAAQAIAAAGAEQAFAFABAIIgIABQgBgFgDgDQgDgDgEAAQgHAAgDAFQgFAFAAAJQAAALAFAEQAEAFAFAAQAFAAAEgDQADgDABgHIAIABQgBAJgGAFQgFAFgJAAQgJAAgHgHg");
  this.shape_21.setTransform(834.1,116.5);

  this.shape_22 = new cjs.Shape();
  this.shape_22.graphics.f("#333333").s().p("AgNAlIAUhJIAHAAIgUBJg");
  this.shape_22.setTransform(830,115.5);

  this.shape_23 = new cjs.Shape();
  this.shape_23.graphics.f("#333333").s().p("AAMAkIAAghQAAgGgDgDQgCgDgGAAQgDAAgDACQgDACgCAEQgBACAAAGIAAAdIgJAAIAAhHIAJAAIAAAaQAGgHAIAAQAGAAAEACQAEACACAEQACAEAAAHIAAAhg");
  this.shape_23.setTransform(825.8,115.5);

  this.shape_24 = new cjs.Shape();
  this.shape_24.graphics.f("#333333").s().p("AANAaIAAgeIgBgIIgEgFQgDgBgDAAQgFAAgEAEQgEADAAAKIAAAbIgJAAIAAgyIAIAAIAAAHQAGgJAKAAQAEAAAEACQAEACACACQACADABAEIAAAJIAAAeg");
  this.shape_24.setTransform(820.3,116.5);

  this.shape_25 = new cjs.Shape();
  this.shape_25.graphics.f("#333333").s().p("AgSAXQgFgEAAgHQAAgDACgDQACgEADgCIAFgBIAIgCQAKgBAFgCIAAgCQAAgFgCgDQgEgDgGAAQgGAAgCACQgEADgBAFIgJgBQACgGADgDQACgDAFgCQAFgCAGAAQAGAAAFABQAEACACADQACACAAADIABAIIAAALIAAAQIADAGIgKAAIgBgHQgFAFgFACQgDABgFAAQgJAAgEgEgAgBADIgIACIgEADIgBAEQABADACADQADADAFgBQAEABAEgDQAEgCACgEIABgJIAAgDQgFACgIABg");
  this.shape_25.setTransform(814.7,116.5);

  this.shape_26 = new cjs.Shape();
  this.shape_26.graphics.f("#333333").s().p("AgPAUQgHgHAAgNQAAgHADgHQADgFAGgEQAFgDAGAAQAJAAAFAEQAFAFABAIIgIABQgBgFgDgDQgDgDgFAAQgFAAgEAFQgEAFgBAJQABALAEAEQADAFAGAAQAFAAAEgDQADgDABgHIAIABQgBAJgFAFQgHAFgIAAQgKAAgGgHg");
  this.shape_26.setTransform(806.8,116.5);

  this.shape_27 = new cjs.Shape();
  this.shape_27.graphics.f("#333333").s().p("AgLAjQgEgCgCgCQgCgDgBgEIAAgIIAAgfIAIAAIAAAcIABAJQABADACACQAEACADAAQAEAAADgCQADgCACgDQABgEAAgGIAAgbIAJAAIAAAyIgIAAIAAgHQgGAJgKgBQgEAAgEgBgAgEgWIAFgOIAMAAIgLAOg");
  this.shape_27.setTransform(801.4,115.6);

  this.shape_28 = new cjs.Shape();
  this.shape_28.graphics.f("#333333").s().p("AAMAkIAAghQAAgGgDgDQgCgDgGAAQgDAAgDACQgDACgCAEQgBACAAAGIAAAdIgJAAIAAhHIAJAAIAAAaQAGgHAIAAQAGAAAEACQAEACACAEQACAEAAAHIAAAhg");
  this.shape_28.setTransform(795.8,115.5);

  this.shape_29 = new cjs.Shape();
  this.shape_29.graphics.f("#333333").s().p("AgQAgQgHgEgEgJQgEgJAAgKQAAgKAFgJQAEgIAIgFQAIgEAIAAQALAAAHAFQAHAGADAKIgJACQgDgIgEgDQgFgEgHABQgHAAgGADQgGAEgCAHQgCAGAAAHQAAAJADAHQACAGAGAEQAGADAFAAQAIAAAGgEQAFgFACgJIAKACQgDAMgIAGQgIAGgLAAQgLAAgHgFg");
  this.shape_29.setTransform(789.5,115.5);

  this.shape_30 = new cjs.Shape();
  this.shape_30.graphics.f("#333333").s().p("AgEAkIAAgKIAJAAIAAAKgAgBASIgEglIAAgQIALAAIAAAQIgDAlg");
  this.shape_30.setTransform(925.3,102.4);

  this.shape_31 = new cjs.Shape();
  this.shape_31.graphics.f("#333333").s().p("AgLAZQgEgCgCgCQgCgDgBgEIAAgIIAAggIAIAAIAAAdIABAJQABADACACQAEACADAAQAEAAADgCQADgCACgDQABgEAAgGIAAgcIAJAAIAAAzIgIAAIAAgHQgGAJgJgBQgFAAgEgBg");
  this.shape_31.setTransform(921.1,103.4);

  this.shape_32 = new cjs.Shape();
  this.shape_32.graphics.f("#333333").s().p("AgQAmQgHgHAAgMQAAgNAHgHQAHgHAKAAQAKAAAHAHQAGAGAAANIAAACIgmAAQABAJAEAFQAEAEAGAAQAFAAADgCQADgDACgFIAJABQgBAIgHAEQgFAEgJAAQgLAAgGgHgAgIACQgFAEgBAHIAcAAQAAgHgDgDQgEgEgGAAQgGAAgDADgAAFgOIgFgIIgFAIIgJAAIAKgNIAIAAIALANgAgCgfIgLgNIAMAAIAFANg");
  this.shape_32.setTransform(915.5,101.6);

  this.shape_33 = new cjs.Shape();
  this.shape_33.graphics.f("#333333").s().p("AgEAkIAAgzIAIAAIAAAzgAgEgZIAAgKIAIAAIAAAKg");
  this.shape_33.setTransform(911.7,102.4);

  this.shape_34 = new cjs.Shape();
  this.shape_34.graphics.f("#333333").s().p("AAMAkIAAghQAAgGgDgDQgCgDgGAAQgDAAgDACQgDACgCAEQgBACAAAGIAAAdIgJAAIAAhHIAJAAIAAAaQAGgHAIAAQAGAAAEACQAEACACAEQACAEAAAHIAAAhg");
  this.shape_34.setTransform(907.8,102.4);

  this.shape_35 = new cjs.Shape();
  this.shape_35.graphics.f("#333333").s().p("AAMAaIAAgeIgBgIQAAgDgDgCQgDgBgEAAQgEAAgEAEQgEADAAAKIAAAbIgJAAIAAgzIAIAAIAAAIQAFgJAKAAQAFAAAEACQAEACACACQACADAAAEIABAJIAAAeg");
  this.shape_35.setTransform(902.2,103.3);

  this.shape_36 = new cjs.Shape();
  this.shape_36.graphics.f("#333333").s().p("AgBAiQgCgCgBgCQgBgDAAgHIAAgdIgGAAIAAgHIAGAAIAAgNIAIgFIAAASIAJAAIAAAHIgJAAIAAAdIAAAFIACACIACAAIAFAAIABAIIgHAAQgFAAgCgBg");
  this.shape_36.setTransform(895.3,102.5);

  this.shape_37 = new cjs.Shape();
  this.shape_37.graphics.f("#333333").s().p("AgSApQgFgEAAgHQAAgDACgEQABgDADgBIAHgDIAIgCQAJgBAFgCIAAgCQAAgFgCgDQgEgCgGAAQgFAAgEACQgCACgCAFIgIgBQABgGADgCQACgDAFgCQAGgCAFAAQAHAAAEABQAEACACACQACADAAACIABAIIAAAMIAAAQIADAGIgJAAIgCgHQgFAFgFACQgDABgFAAQgJAAgEgEgAgBAVIgIACIgDADIgCAEQAAADADADQADADAFgBQAEABAEgDQAEgCACgEIABgJIAAgDQgEACgJABgAAGgOIgGgIIgFAIIgKAAIALgNIAJAAIALANgAgEgfIAFgNIANAAIgMANg");
  this.shape_37.setTransform(891.1,101.6);

  this.shape_38 = new cjs.Shape();
  this.shape_38.graphics.f("#333333").s().p("AgNAaIAAgzIAIAAIAAAIQADgFACgCQACgCADAAQAEAAAFAEIgDAIQgDgDgDAAQgDAAgCADQgCABgBADIgBAKIAAAag");
  this.shape_38.setTransform(887,103.3);

  this.shape_39 = new cjs.Shape();
  this.shape_39.graphics.f("#333333").s().p("AgEAsIAAgKIAJAAIAAAKgAgEAcIAAgzIAIAAIAAAzgAgEghIAAgKIAIAAIAAAKg");
  this.shape_39.setTransform(881.1,103.2);

  this.shape_40 = new cjs.Shape();
  this.shape_40.graphics.f("#333333").s().p("AAMAkIAAghQAAgGgDgDQgCgDgGAAQgDAAgDACQgDACgCAEQgBACAAAGIAAAdIgJAAIAAhHIAJAAIAAAaQAGgHAIAAQAGAAAEACQAEACACAEQACAEAAAHIAAAhg");
  this.shape_40.setTransform(877.2,102.4);

  this.shape_41 = new cjs.Shape();
  this.shape_41.graphics.f("#333333").s().p("AgPAUQgHgHAAgMQAAgIADgHQADgFAGgEQAFgDAGAAQAJAAAFAEQAFAFABAIIgIABQgBgFgDgDQgDgDgFAAQgFAAgEAFQgEAFgBAJQABALAEAEQADAFAGAAQAFAAAEgDQADgDABgHIAIABQgBAJgFAFQgHAFgIAAQgKAAgGgHg");
  this.shape_41.setTransform(872.1,103.4);

  this.shape_42 = new cjs.Shape();
  this.shape_42.graphics.f("#333333").s().p("AgNAlIAUhJIAHAAIgUBJg");
  this.shape_42.setTransform(868,102.4);

  this.shape_43 = new cjs.Shape();
  this.shape_43.graphics.f("#333333").s().p("AAMAkIAAghQAAgGgDgDQgCgDgGAAQgDAAgDACQgDACgCAEQgBACAAAGIAAAdIgJAAIAAhHIAJAAIAAAaQAGgHAIAAQAGAAAEACQAEACACAEQACAEAAAHIAAAhg");
  this.shape_43.setTransform(863.8,102.4);

  this.shape_44 = new cjs.Shape();
  this.shape_44.graphics.f("#333333").s().p("AANAaIAAgeIgBgIIgEgFQgDgBgDAAQgFAAgEAEQgEADAAAKIAAAbIgJAAIAAgzIAIAAIAAAIQAFgJAKAAQAFAAAEACQAEACACACQACADABAEIAAAJIAAAeg");
  this.shape_44.setTransform(858.3,103.3);

  this.shape_45 = new cjs.Shape();
  this.shape_45.graphics.f("#333333").s().p("AgSAXQgFgEAAgHQAAgDACgEQACgDADgBIAGgCIAIgCQAJgBAFgCIAAgCQAAgFgCgDQgEgDgGAAQgGAAgCADQgDACgCAFIgJgBQACgGADgDQACgDAFgCQAGgCAFAAQAGAAAFABQAEACACACQACADAAADIABAIIAAALIAAAQIADAGIgJAAIgCgHQgFAFgFACQgDABgFAAQgJAAgEgEgAgBADIgIACIgEADIgBAEQAAADADADQADADAFgBQAEABAEgDQAEgCACgEIABgJIAAgDQgFACgIABg");
  this.shape_45.setTransform(852.7,103.4);

  this.shape_46 = new cjs.Shape();
  this.shape_46.graphics.f("#333333").s().p("AANAaIAAgeIgCgIIgDgFQgDgBgEAAQgEAAgEAEQgEADAAAKIAAAbIgJAAIAAgzIAIAAIAAAIQAGgJAKAAQAEAAAEACQAEACACACQACADAAAEIABAJIAAAeg");
  this.shape_46.setTransform(844.4,103.3);

  this.shape_47 = new cjs.Shape();
  this.shape_47.graphics.f("#333333").s().p("AgWAUQgGgHgBgNQABgNAHgIQAHgFAKAAQAKAAAGAHQAFAGACAHIAEgDIABgHIgEAAIAAgKIAKAAIAAAIQAAAGgCADQgDAFgGADIAAABQAAASgMAGQgGADgFAAQgLAAgHgHgAgPgOQgFAGAAAIQAAAKAFAFQAEAFAHAAQAFAAAEgFQAFgFAAgKQAAgIgFgGQgEgFgFAAQgHAAgEAFg");
  this.shape_47.setTransform(838.4,103.4);

  this.shape_48 = new cjs.Shape();
  this.shape_48.graphics.f("#333333").s().p("AAaAaIAAgfIAAgHQgBgDgDgCQgCgBgDAAQgFAAgEAEQgEADAAAJIAAAcIgHAAIAAggQAAgGgCgDQgDgDgFAAQgDAAgDACQgEACgBAEQgBAEAAAGIAAAaIgJAAIAAgzIAIAAIAAAIQACgEAEgCQAEgDAFAAQAGAAAEADQADACABAEQAGgIAKgBQAIAAAEAFQAEAEAAAJIAAAig");
  this.shape_48.setTransform(828.1,103.3);

  this.shape_49 = new cjs.Shape();
  this.shape_49.graphics.f("#333333").s().p("AgSAiQgFgEAAgGQAAgEACgDQACgEADgBIAGgDIAHgCQAKgBAFgCIAAgCQAAgEgCgDQgEgDgGAAQgGAAgCADQgDACgCAEIgJgBQACgEADgEQACgDAFgCQAFgCAGAAQAGAAAFACQAEABACADQACACAAAEIABAHIAAAMIAAAPIADAGIgKAAIgBgGQgFAEgFACQgDABgFAAQgJAAgEgEgAgBAPIgIABIgEADIgBAEQAAAEADACQADADAFAAQAEAAAEgDQAEgCACgEIABgJIAAgDQgFACgIACgAAAgUIAAgGIACAAQABAAAAgBQABAAAAgBQAAAAAAAAQAAgBAAAAIgBgCIgGgBIgEAAIAAgFIAHAAQAEAAAFACQADACAAADQABAGgJACIAAACg");
  this.shape_49.setTransform(821.1,102.2);

  this.shape_50 = new cjs.Shape();
  this.shape_50.graphics.f("#333333").s().p("AgQAgQgHgEgEgJQgEgJAAgKQAAgKAFgJQAEgIAIgFQAIgEAIAAQALAAAHAFQAHAGADAKIgJACQgDgHgEgEQgFgDgHAAQgHAAgGAEQgGADgCAHQgCAGAAAHQAAAJADAGQACAHAGAEQAGADAFAAQAIAAAGgFQAFgEACgJIAKACQgDAMgIAGQgIAGgLAAQgLAAgHgFg");
  this.shape_50.setTransform(814.8,102.4);

  this.timeline.addTween(cjs.Tween.get({}).to({state:[{t:this.shape_50},{t:this.shape_49},{t:this.shape_48},{t:this.shape_47},{t:this.shape_46},{t:this.shape_45},{t:this.shape_44},{t:this.shape_43},{t:this.shape_42},{t:this.shape_41},{t:this.shape_40},{t:this.shape_39},{t:this.shape_38},{t:this.shape_37},{t:this.shape_36},{t:this.shape_35},{t:this.shape_34},{t:this.shape_33},{t:this.shape_32},{t:this.shape_31},{t:this.shape_30},{t:this.shape_29},{t:this.shape_28},{t:this.shape_27},{t:this.shape_26},{t:this.shape_25},{t:this.shape_24},{t:this.shape_23},{t:this.shape_22},{t:this.shape_21},{t:this.shape_20},{t:this.shape_19},{t:this.shape_18},{t:this.shape_17},{t:this.shape_16},{t:this.shape_15},{t:this.shape_14},{t:this.shape_13},{t:this.shape_12},{t:this.shape_11},{t:this.shape_10},{t:this.shape_9},{t:this.shape_8},{t:this.shape_7},{t:this.shape_6},{t:this.shape_5},{t:this.shape_4},{t:this.shape_3},{t:this.shape_2},{t:this.shape_1},{t:this.shape}]}).wait(19));

  // notice_box
  this.shape_51 = new cjs.Shape();
  this.shape_51.graphics.f().s("#999999").ss(1,1,1).p("AOKAAQAACwkJB9QkKB9l3AAQl3AAkJh9QkJh9AAiwQAAivEJh9QEJh9F3AAQF3AAEKB9QEJB9AACvg");
  this.shape_51.setTransform(871.6,112.6);

  this.shape_52 = new cjs.Shape();
  this.shape_52.graphics.f("#FFFFFF").s().p("AqAEuQkJh+gBiwQABiwEJh8QEKh+F2ABQF3gBEKB+QEJB8ABCwQgBCwkJB+QkKB8l3AAQl2AAkKh8g");
  this.shape_52.setTransform(871.6,112.6);

  this.timeline.addTween(cjs.Tween.get({}).to({state:[{t:this.shape_52},{t:this.shape_51}]}).wait(19));

}).prototype = p_step5 = new cjs.MovieClip();
p_step5.nominalBounds = new cjs.Rectangle(1419.6,415,289.4,134.3);
// library properties:
lib_step5.properties = {
  id: '9D33D0B2ABC183428F26CA73B2961738_5',
  width: 1280,
  height: 720,
  fps: 24,
  color: "#FFFFFF",
  opacity: 1.00,
  manifest: [
    {src:"/bitrix/components/vportal/vivi_notify/templates/.default/images/happy.png?1533277867507", id:"happy"}
  ],
  preloads: []
};

// bootstrap callback support:
(lib_step5.Stage = function(canvas) {
  createjs.Stage.call(this, canvas);
}).prototype = p_step5 = new createjs.Stage();

p_step5.setAutoPlay = function(autoPlay) {
  this.tickEnabled = autoPlay;
}
p_step5.play = function() { this.tickEnabled = true; this.getChildAt(0).gotoAndPlay(this.getTimelinePosition()) }
p_step5.stop = function(ms) { if(ms) this.seek(ms); this.tickEnabled = false; }
p_step5.seek = function(ms) { this.tickEnabled = true; this.getChildAt(0).gotoAndStop(lib_step5.properties.fps * ms / 1000); }
p_step5.getDuration = function() { return this.getChildAt(0).totalFrames / lib_step5.properties.fps * 1000; }

p_step5.getTimelinePosition = function() { return this.getChildAt(0).currentFrame / lib_step5.properties.fps * 1000; }

an.bootcompsLoaded = an.bootcompsLoaded || [];
if(!an.bootstrapListeners) {
  an.bootstrapListeners=[];
}
//STEP5_END
//==============================================================================

//STEP6_BEGIN
var p_step6; // shortcut to reference prototypes
var lib_step6={};var ss_step6={};var img_step6={};
lib_step6.ssMetadata = [];

// symbols:
(lib_step6.suprise = function() {
  this.initialize(img_step6.suprise);
}).prototype = p_step6 = new cjs.Bitmap();
p_step6.nominalBounds = new cjs.Rectangle(0,0,289,290);

// stage content:
(lib_step6.notice1_cảnh6_chờ = function(mode,startPosition,loop) {
  this.initialize(mode,startPosition,false,{});

  // suprise
  this.instance = new lib_step6.suprise();
  this.instance.parent = this;
  this.instance.setTransform(939,57,0.456,0.456);

  this.timeline.addTween(cjs.Tween.get(this.instance).wait(15));

  // text
  this.shape = new cjs.Shape();
  this.shape.graphics.f("#333333").s().p("AgEAkIAAgKIAJAAIAAAKgAgBASIgEglIAAgQIALAAIAAAQIgDAlg");
  this.shape.setTransform(914.9,112.8);

  this.shape_1 = new cjs.Shape();
  this.shape_1.graphics.f("#333333").s().p("AgEAjIAAgLIAJAAIAAALgAgTAPQgEgEAAgGQAAgEACgCQACgDACgCIAGgDIAIgCQAKgBAFgCIAAgBQAAgGgCgDQgDgCgHAAQgFAAgEACQgDACgBAGIgIgBQABgGACgDQADgEAFgCQAFgBAGAAQAGgBAFACQAEACACACQACACABAEIAAAIIAAAMIABAOIACAHIgKAAIgBgHQgFAEgEACQgEABgFABQgIAAgGgFgAgBgDIgIABIgDADIgBADQgBAEADADQADACAFAAQAEAAAEgCQAEgCACgFIABgIIAAgCQgFACgIABg");
  this.shape_1.setTransform(910.7,114.5);

  this.shape_2 = new cjs.Shape();
  this.shape_2.graphics.f("#333333").s().p("AgEAsIAAgKIAJAAIAAAKgAgEAcIAAgzIAJAAIAAAzgAgEghIAAgKIAJAAIAAAKg");
  this.shape_2.setTransform(904.1,113.6);

  this.shape_3 = new cjs.Shape();
  this.shape_3.graphics.f("#333333").s().p("AAMAkIAAghQAAgGgDgDQgCgDgGAAQgDAAgDACQgDACgCAEQgBACAAAGIAAAdIgJAAIAAhHIAJAAIAAAaQAGgHAIAAQAGAAAEACQAEACACAEQACAEAAAHIAAAhg");
  this.shape_3.setTransform(900.2,112.8);

  this.shape_4 = new cjs.Shape();
  this.shape_4.graphics.f("#333333").s().p("AgPAUQgGgGgBgOQABgHACgGQADgHAFgDQAHgDAFAAQAIAAAGAFQAFAEACAHIgJABQgBgFgDgCQgDgDgEAAQgGAAgFAFQgEAFABAJQgBAKAEAFQAFAFAFAAQAFAAAEgDQADgDABgHIAIABQgBAJgGAFQgFAFgJAAQgJAAgHgHg");
  this.shape_4.setTransform(895.1,113.8);

  this.shape_5 = new cjs.Shape();
  this.shape_5.graphics.f("#333333").s().p("AgNAlIAUhJIAHAAIgUBJg");
  this.shape_5.setTransform(891,112.8);

  this.shape_6 = new cjs.Shape();
  this.shape_6.graphics.f("#333333").s().p("AAMAkIAAghQAAgGgDgDQgCgDgGAAQgDAAgDACQgDACgCAEQgBACAAAGIAAAdIgJAAIAAhHIAJAAIAAAaQAGgHAIAAQAGAAAEACQAEACACAEQACAEAAAHIAAAhg");
  this.shape_6.setTransform(886.8,112.8);

  this.shape_7 = new cjs.Shape();
  this.shape_7.graphics.f("#333333").s().p("AANAbIAAgfIgCgIIgDgEQgDgCgEAAQgEAAgEADQgEAEAAAKIAAAcIgJAAIAAgzIAIAAIAAAHQAGgIAKAAQAEgBAEACQAEACACADQACADAAADIABAIIAAAgg");
  this.shape_7.setTransform(881.3,113.7);

  this.shape_8 = new cjs.Shape();
  this.shape_8.graphics.f("#333333").s().p("AgTAXQgEgEAAgGQAAgFACgDQABgDAEgCIAFgCIAIgBQAKgBAFgCIAAgCQAAgGgCgCQgDgDgHAAQgFAAgEACQgCADgCAFIgIgBQABgGACgDQADgEAFgBQAGgCAFAAQAHAAAEACQAEABACACQACADABAEIAAAIIAAALIABAPIACAGIgKAAIgBgGQgFAEgEABQgEACgFAAQgIAAgGgEgAgBAEIgIABIgEADIAAAEQgBADADADQADADAFAAQAEAAAEgDQAEgCACgEIABgJIAAgDQgFACgIACg");
  this.shape_8.setTransform(875.7,113.8);

  this.shape_9 = new cjs.Shape();
  this.shape_9.graphics.f("#333333").s().p("AgWAdQgHgGAAgNQABgOAHgHQAHgFAJAAQAKgBAHAIQAFAFACAHIAEgDIABgHIgEAAIAAgJIAJAAIAAAIQAAAGgBACQgCAEgHAEIAAABQAAASgMAHQgFADgHAAQgLAAgGgIgAgPgFQgEAFgBAKQABAJAEAGQAEAEAGAAQAHAAADgEQAFgGAAgJQAAgKgFgFQgDgEgHAAQgGAAgEAEgAgGgWIgLgNIAMAAIAGANg");
  this.shape_9.setTransform(866.9,112.8);

  this.shape_10 = new cjs.Shape();
  this.shape_10.graphics.f("#333333").s().p("AAMAkIAAghQAAgGgDgDQgCgDgGAAQgDAAgDACQgDACgCAEQgBACAAAGIAAAdIgJAAIAAhHIAJAAIAAAaQAGgHAIAAQAGAAAEACQAEACACAEQACAEAAAHIAAAhg");
  this.shape_10.setTransform(860.8,112.8);

  this.shape_11 = new cjs.Shape();
  this.shape_11.graphics.f("#333333").s().p("AgPAUQgGgGAAgOQgBgHADgGQADgHAFgDQAGgDAGAAQAIAAAGAFQAFAEACAHIgJABQgBgFgDgCQgDgDgFAAQgGAAgEAFQgDAFAAAJQAAAKADAFQAEAFAGAAQAFAAADgDQAEgDABgHIAJABQgCAJgGAFQgGAFgIAAQgKAAgGgHg");
  this.shape_11.setTransform(855.7,113.8);

  this.shape_12 = new cjs.Shape();
  this.shape_12.graphics.f("#333333").s().p("AgQAdQgHgHAAgNQAAgMAHgHQAHgHAKAAQAKAAAHAHQAGAHAAAMIAAADIgmAAQAAAIAFAEQAEAFAGAAQAFAAADgDQADgCACgGIAJACQgBAHgHAFQgFAEgJAAQgKAAgHgHgAgIgFQgFAEgBAFIAcAAQAAgFgCgDQgFgGgGAAQgGAAgDAFgAAAgbIgEgBIgDABIgBAEIgGAAQAAgGACgCQADgDAEgBQADAAAFADIAEACIADgBIABgEIAGAAQAAAGgDACQgCAEgFAAQgDAAgEgEg");
  this.shape_12.setTransform(847.5,112.9);

  this.shape_13 = new cjs.Shape();
  this.shape_13.graphics.f("#333333").s().p("AgOAXQgFgEgCgIIAJgCQABAFAEADQACADAGAAQAGAAADgDQACgCAAgDQAAgEgCgBIgJgDIgNgEQgDgBgCgDQgCgDAAgEQAAgDACgEQABgCADgCIAGgDIAHgBQAFAAAFACQAEACADACQACAEABAEIgJABQgBgDgCgDQgDgCgFAAQgGAAgCACQgDACAAADIACADIADADIAHACIANADQAEABACADQABADAAAFQABAEgDAEQgDAEgFACQgEACgGAAQgKAAgFgEg");
  this.shape_13.setTransform(842.2,113.8);

  this.shape_14 = new cjs.Shape();
  this.shape_14.graphics.f("#333333").s().p("AgLAjQgEgCgCgDQgCgCgBgEIAAgIIAAgfIAJAAIAAAbIAAAJQABAEACACQAEACADAAQADAAAEgCQAEgCAAgEQACgDAAgHIAAgaIAJAAIAAAzIgIAAIAAgIQgGAJgJAAQgFgBgEgBgAgEgWIAFgNIAMAAIgLANg");
  this.shape_14.setTransform(834.1,112.8);

  this.shape_15 = new cjs.Shape();
  this.shape_15.graphics.f("#333333").s().p("AgQAhQgHgFgEgJQgEgJAAgKQAAgLAFgIQAEgIAIgEQAIgFAIAAQALAAAHAGQAHAFADAKIgJACQgDgHgEgEQgFgDgHgBQgHAAgGAFQgGADgCAHQgCAHAAAGQAAAJADAGQACAIAGACQAGAEAFAAQAIAAAGgFQAFgEACgJIAKADQgDALgIAGQgIAGgLAAQgLAAgHgEg");
  this.shape_15.setTransform(827.8,112.8);

  this.timeline.addTween(cjs.Tween.get({}).to({state:[{t:this.shape_15},{t:this.shape_14},{t:this.shape_13},{t:this.shape_12},{t:this.shape_11},{t:this.shape_10},{t:this.shape_9},{t:this.shape_8},{t:this.shape_7},{t:this.shape_6},{t:this.shape_5},{t:this.shape_4},{t:this.shape_3},{t:this.shape_2},{t:this.shape_1},{t:this.shape}]}).wait(15));

  // notice_box
  this.shape_16 = new cjs.Shape();
  this.shape_16.graphics.f().s("#999999").ss(1,1,1).p("AOKAAQAACwkJB9QkKB9l3AAQl3AAkJh9QkJh9AAiwQAAivEJh9QEJh9F3AAQF3AAEKB9QEJB9AACvg");
  this.shape_16.setTransform(871.6,112.6);

  this.shape_17 = new cjs.Shape();
  this.shape_17.graphics.f("#FFFFFF").s().p("AqAEuQkJh+gBiwQABiwEJh8QEKh+F2ABQF3gBEKB+QEJB8ABCwQgBCwkJB+QkKB8l3AAQl2AAkKh8g");
  this.shape_17.setTransform(871.6,112.6);

  this.timeline.addTween(cjs.Tween.get({}).to({state:[{t:this.shape_17},{t:this.shape_16}]}).wait(15));

}).prototype = p_step6 = new cjs.MovieClip();
p_step6.nominalBounds = new cjs.Rectangle(1420,417,290.8,132.3);
// lib_step6 properties:
lib_step6.properties = {
  id: '9D33D0B2ABC183428F26CA73B2961738_6',
  width: 1280,
  height: 720,
  fps: 24,
  color: "#FFFFFF",
  opacity: 1.00,
  manifest: [
    {src:"/bitrix/components/vportal/vivi_notify/templates/.default/images/suprise.png?1533277882842", id:"suprise"}
  ],
  preloads: []
};

(lib_step6.Stage = function(canvas) {
  createjs.Stage.call(this, canvas);
}).prototype = p_step6 = new createjs.Stage();

p_step6.setAutoPlay = function(autoPlay) {
  this.tickEnabled = autoPlay;
}
p_step6.play = function() { this.tickEnabled = true; this.getChildAt(0).gotoAndPlay(this.getTimelinePosition()) }
p_step6.stop = function(ms) { if(ms) this.seek(ms); this.tickEnabled = false; }
p_step6.seek = function(ms) { this.tickEnabled = true; this.getChildAt(0).gotoAndStop(lib_step6.properties.fps * ms / 1000); }
p_step6.getDuration = function() { return this.getChildAt(0).totalFrames / lib_step6.properties.fps * 1000; }

p_step6.getTimelinePosition = function() { return this.getChildAt(0).currentFrame / lib_step6.properties.fps * 1000; }

//STEP6_END
//===========================================================================================
//STEP7_BEGIN
var p_step7; // shortcut to reference prototypes
var lib_step7={};var ss_step7={};var img_step7={};
lib_step7.ssMetadata = [
    {name:"notice1_cảnh7_đồng hồ chạy_ ngủ_atlas_", frames: [[0,514,273,265],[0,0,512,512]]}
];

// symbols:
(lib_step7.asleep = function() {
  this.spriteSheet = ss_step7["notice1_cảnh7_đồng hồ chạy_ ngủ_atlas_"];
  this.gotoAndStop(0);
}).prototype = p_step7 = new cjs.Sprite();

(lib_step7.clock1 = function() {
  this.spriteSheet = ss_step7["notice1_cảnh7_đồng hồ chạy_ ngủ_atlas_"];
  this.gotoAndStop(1);
}).prototype = p_step7 = new cjs.Sprite();


// stage content:
(lib_step7.notice1_cảnh7_đồnghồchạyngủ = function(mode,startPosition,loop) {
  this.initialize(mode,startPosition,false,{});

  // asleep
  this.instance = new lib_step7.asleep();
  this.instance.parent = this;
  this.instance.setTransform(945,71,0.446,0.446);
  this.instance._off = false;

  this.timeline.addTween(cjs.Tween.get(this.instance).wait(35).to({_off:true},0).wait(15));

  // clock
  this.instance_1 = new lib_step7.clock1();
  this.instance_1.parent = this;
  this.instance_1.setTransform(861,91,0.222,0.222);
  this.instance_1._off = false;

  this.timeline.addTween(cjs.Tween.get(this.instance_1).wait(35).to({_off:true},0).wait(15));

}).prototype = p_step7 = new cjs.MovieClip();
p_step7.nominalBounds = null;
// lib_step7rary properties:
lib_step7.properties = {
  id: '9D33D0B2ABC183428F26CA73B2961738_7',
  width: 1280,
  height: 720,
  fps: 24,
  color: "#FFFFFF",
  opacity: 1.00,
  manifest: [
    {src:"/bitrix/components/vportal/vivi_notify/templates/.default/images/notice1_cảnh7_đồng hồ chạy_ ngủ_atlas_.png?1533277954400", id:"notice1_cảnh7_đồng hồ chạy_ ngủ_atlas_"}
  ],
  preloads: []
};

// bootstrap callback support:
(lib_step7.Stage = function(canvas) {
  createjs.Stage.call(this, canvas);
}).prototype = p_step7 = new createjs.Stage();

p_step7.setAutoPlay = function(autoPlay) {
  this.tickEnabled = autoPlay;
}
p_step7.play = function() { this.tickEnabled = true; this.getChildAt(0).gotoAndPlay(this.getTimelinePosition()) }
p_step7.stop = function(ms) { if(ms) this.seek(ms); this.tickEnabled = false; }
p_step7.seek = function(ms) { this.tickEnabled = true; this.getChildAt(0).gotoAndStop(lib_step7.properties.fps * ms / 1000); }
p_step7.getDuration = function() { return this.getChildAt(0).totalFrames / lib_step7.properties.fps * 1000; }

p_step7.getTimelinePosition = function() { return this.getChildAt(0).currentFrame / lib_step7.properties.fps * 1000; }

//STEP7_END
//============================================================================================
//STEP8_BEGIN
var p_step8; // shortcut to reference prototypes
var lib_step8={};var ss_step8={};var img_step8={};
lib_step8.ssMetadata = [];

// symbols:
(lib_step8.happy = function() {
  this.initialize(img_step8.happy);
}).prototype = p_step8 = new cjs.Bitmap();
p_step8.nominalBounds = new cjs.Rectangle(0,0,297,302);

// stage content:
(lib_step8.notice1_cảnh8_mờiđọc = function(mode,startPosition,loop) {
  this.initialize(mode,startPosition,false,{});

  // happy
  this.instance = new lib_step8.happy();
  this.instance.parent = this;
  this.instance.setTransform(937,55,0.445,0.445);

  this.timeline.addTween(cjs.Tween.get(this.instance).wait(9));

  // text
  this.shape = new cjs.Shape();
  this.shape.graphics.f("#333333").s().p("AgEAkIAAgKIAJAAIAAAKgAgCASIgDglIAAgQIAKAAIAAAQIgCAlg");
  this.shape.setTransform(933.5,112.8);

  this.shape_1 = new cjs.Shape();
  this.shape_1.graphics.f("#333333").s().p("AgEAjIAAgLIAJAAIAAALgAgSAPQgFgEAAgGQAAgEACgCQABgDADgCIAHgDIAIgCQAJgBAFgCIAAgBQAAgGgCgDQgEgCgGAAQgFAAgEACQgCACgCAGIgIgBQABgGADgDQACgEAFgCQAGgBAFAAQAHgBAEACQAEACACACQACACAAAEIABAIIAAAMIAAAOIADAHIgJAAIgCgHQgFAEgFACQgDABgFABQgJAAgEgFgAgBgDIgIABIgDADIgCADQAAAEADADQADACAFAAQAEAAAEgCQAEgCACgFIABgIIAAgCQgEACgJABg");
  this.shape_1.setTransform(929.3,114.5);

  this.shape_2 = new cjs.Shape();
  this.shape_2.graphics.f("#333333").s().p("AgRAUQgGgHAAgNQAAgNAIgIQAGgFAJAAQALAAAGAHQAHAHAAAMQAAAJgDAGQgDAGgGADQgFADgHAAQgKAAgHgHgAgKgOQgEAFAAAJQAAAKAEAFQAEAFAGAAQAGAAAFgFQAEgFAAgKQAAgJgEgFQgFgFgGAAQgGAAgEAFg");
  this.shape_2.setTransform(921,113.8);

  this.shape_3 = new cjs.Shape();
  this.shape_3.graphics.f("#333333").s().p("AgTAgQgEgEAAgGQAAgEACgDQABgDAEgCIAFgDIAIgCQAKgBAFgCIAAgBQAAgFgCgDQgEgCgGAAQgGAAgCACQgEACgBAFIgJAAQACgGADgDQACgEAFgCQAFgBAGAAQAHgBAEACQAEACACACQACACABAEIAAAHIAAAMIAAAPIADAHIgKAAIgBgHQgFAEgEACQgEABgFABQgIAAgGgFgAgBANIgIABIgEAEIAAADQAAAEACADQADACAFAAQAEAAAEgCQAEgCACgFIABgJIAAgCQgEACgJABgAgEgWIAGgNIALAAIgKANg");
  this.shape_3.setTransform(915.4,112.8);

  this.shape_4 = new cjs.Shape();
  this.shape_4.graphics.f("#333333").s().p("AgOAdIAAAGIgIAAIAAhGIAJAAIAAAZQAGgHAIAAQAEAAAFACQAEACACAEQAEADABAFQACAEgBAGQAAANgGAHQgHAHgJAAQgIAAgGgHgAgJgFQgEAFgBAJQABAJACAEQAEAHAHAAQAFAAAFgFQADgFAAgKQAAgJgDgFQgFgFgFAAQgFAAgEAFg");
  this.shape_4.setTransform(910,112.8);

  this.shape_5 = new cjs.Shape();
  this.shape_5.graphics.f("#333333").s().p("AgOAhQgGgFAAgHIAIABQABADACADQAEACAFAAQAGAAADgCQADgDACgEIAAgMQgGAHgIAAQgKAAgGgIQgGgHAAgKQAAgIADgGQADgGAEgDQAGgEAGAAQAJAAAGAHIAAgGIAIAAIAAAsQAAAMgCAFQgDAFgFADQgGADgHAAQgJAAgFgEgAgJgYQgEAFABAJQgBAKAEAEQAFAFAFgBQAFABAFgFQADgEAAgKQAAgJgDgFQgFgFgFAAQgFAAgFAFg");
  this.shape_5.setTransform(901.4,114.8);

  this.shape_6 = new cjs.Shape();
  this.shape_6.graphics.f("#333333").s().p("AANAbIAAgfIgBgIIgEgEQgDgCgDAAQgFAAgEADQgEAEAAAKIAAAcIgJAAIAAgzIAIAAIAAAHQAFgIALAAQAEgBAEACQAEACACADQACADABADIAAAIIAAAgg");
  this.shape_6.setTransform(896,113.7);

  this.shape_7 = new cjs.Shape();
  this.shape_7.graphics.f("#333333").s().p("AgRAdQgGgGAAgNQAAgOAIgHQAGgFAJAAQALgBAGAIQAHAGAAAMQAAAKgDAGQgDAFgGAEQgFADgHAAQgKAAgHgIgAgKgFQgEAFAAAKQAAAJAEAGQAEAEAGAAQAGAAAFgEQAEgGAAgJQAAgKgEgFQgFgEgGAAQgGAAgEAEgAAFgWIgFgIIgFAIIgKAAIALgNIAIAAIALANg");
  this.shape_7.setTransform(890.4,112.8);

  this.shape_8 = new cjs.Shape();
  this.shape_8.graphics.f("#333333").s().p("AAMAkIAAghQAAgGgDgDQgCgDgGAAQgDAAgDACQgDACgCAEQgBACAAAGIAAAdIgJAAIAAhHIAJAAIAAAaQAGgHAIAAQAGAAAEACQAEACACAEQACAEAAAHIAAAhg");
  this.shape_8.setTransform(884.9,112.8);

  this.shape_9 = new cjs.Shape();
  this.shape_9.graphics.f("#333333").s().p("AgBAiQgCgCgBgCQgCgDABgHIAAgdIgHAAIAAgHIAHAAIAAgNIAIgFIAAASIAJAAIAAAHIgJAAIAAAdIAAAFIABACIADAAIAFAAIABAIIgHAAQgFAAgCgBg");
  this.shape_9.setTransform(880.8,112.9);

  this.shape_10 = new cjs.Shape();
  this.shape_10.graphics.f("#333333").s().p("AgPAUQgHgGABgOQgBgHADgGQADgHAGgDQAFgDAGAAQAIAAAGAFQAFAEACAHIgJABQgBgFgDgCQgDgDgFAAQgFAAgFAFQgDAFAAAJQAAAKADAFQAFAFAFAAQAFAAADgDQAEgDABgHIAJABQgCAJgFAFQgHAFgIAAQgKAAgGgHg");
  this.shape_10.setTransform(874.2,113.8);

  this.shape_11 = new cjs.Shape();
  this.shape_11.graphics.f("#333333").s().p("AgEAjIAAgLIAJAAIAAALgAgRAMQgGgGAAgMQAAgPAIgHQAGgFAJAAQALgBAGAIQAHAGAAANQAAAJgDAGQgDAFgGAEQgFADgHAAQgKAAgHgIgAgKgWQgEAGAAAKQAAAIAEAGQAEAEAGAAQAGAAAFgEQAEgGAAgIQAAgKgEgGQgFgEgGAAQgGAAgEAEg");
  this.shape_11.setTransform(868.7,114.5);

  this.shape_12 = new cjs.Shape();
  this.shape_12.graphics.f("#333333").s().p("AgOAhQgGgDgCgHQgDgGAAgIQAAgHADgGQACgGAGgDQAFgEAGAAQAEAAADACQAEACACADIAAgNIgPAAIAAgHIAPAAIAAgFIAJAAIAAAFIAHAAIAAAHIgHAAIAAA6IgJAAIAAgGQgEAHgJAAQgGAAgFgDgAgMgFQgEAFAAAJQAAAKAEAFQAFAFAFAAQAFAAAEgEQAEgFAAgKQAAgKgEgFQgFgFgEAAQgGAAgEAFg");
  this.shape_12.setTransform(863.4,112.8);

  this.shape_13 = new cjs.Shape();
  this.shape_13.graphics.f("#333333").s().p("AgEAsIAAgKIAJAAIAAAKgAgDAcIAAgzIAIAAIAAAzgAgDghIAAgKIAIAAIAAAKg");
  this.shape_13.setTransform(856.5,113.6);

  this.shape_14 = new cjs.Shape();
  this.shape_14.graphics.f("#333333").s().p("AAMAkIAAghQAAgGgDgDQgCgDgGAAQgDAAgDACQgDACgCAEQgBACAAAGIAAAdIgJAAIAAhHIAJAAIAAAaQAGgHAIAAQAGAAAEACQAEACACAEQACAEAAAHIAAAhg");
  this.shape_14.setTransform(852.6,112.8);

  this.shape_15 = new cjs.Shape();
  this.shape_15.graphics.f("#333333").s().p("AgPAUQgHgGABgOQAAgHACgGQADgHAGgDQAFgDAGAAQAIAAAGAFQAFAEACAHIgJABQgBgFgDgCQgDgDgFAAQgGAAgEAFQgDAFAAAJQAAAKADAFQAEAFAGAAQAFAAADgDQAEgDABgHIAJABQgCAJgFAFQgHAFgIAAQgKAAgGgHg");
  this.shape_15.setTransform(847.5,113.8);

  this.shape_16 = new cjs.Shape();
  this.shape_16.graphics.f("#333333").s().p("AgNAlIAUhJIAHAAIgUBJg");
  this.shape_16.setTransform(843.5,112.8);

  this.shape_17 = new cjs.Shape();
  this.shape_17.graphics.f("#333333").s().p("AAMAkIAAghQAAgGgDgDQgCgDgGAAQgDAAgDACQgDACgCAEQgBACAAAGIAAAdIgJAAIAAhHIAJAAIAAAaQAGgHAIAAQAGAAAEACQAEACACAEQACAEAAAHIAAAhg");
  this.shape_17.setTransform(839.3,112.8);

  this.shape_18 = new cjs.Shape();
  this.shape_18.graphics.f("#333333").s().p("AAMAbIAAgfIgBgIIgDgEQgDgCgEAAQgEAAgEADQgEAEAAAKIAAAcIgJAAIAAgzIAIAAIAAAHQAFgIAKAAQAFgBAEACQAEACACADQACADAAADIABAIIAAAgg");
  this.shape_18.setTransform(833.7,113.7);

  this.shape_19 = new cjs.Shape();
  this.shape_19.graphics.f("#333333").s().p("AgSAXQgFgEAAgGQAAgFACgDQABgDADgCIAHgCIAIgBQAJgBAFgCIAAgCQAAgGgCgCQgDgDgHAAQgFAAgEACQgDADgBAFIgIgBQABgGACgDQADgEAFgBQAFgCAGAAQAHAAAEACQAEABACACQACADAAAEIABAIIAAALIABAPIACAGIgJAAIgCgGQgFAEgEABQgEACgFAAQgJAAgEgEgAgBAEIgIABIgDADIgBAEQAAADACADQADADAFAAQAEAAAEgDQAEgCACgEIABgJIAAgDQgEACgJACg");
  this.shape_19.setTransform(828.2,113.8);

  this.shape_20 = new cjs.Shape();
  this.shape_20.graphics.f("#333333").s().p("AgDAkIAAgzIAIAAIAAAzgAgDgZIAAgKIAIAAIAAAKg");
  this.shape_20.setTransform(821.5,112.8);

  this.shape_21 = new cjs.Shape();
  this.shape_21.graphics.f("#333333").s().p("AgWAdQgHgGABgNQAAgOAIgHQAGgFAJAAQAKgBAHAIQAFAFABAHIAGgDIABgHIgFAAIAAgJIAJAAIAAAIQAAAGgCACQgCAEgGAEIAAABQAAASgMAHQgFADgHAAQgLAAgGgIgAgQgFQgDAFAAAKQAAAJADAGQAFAEAGAAQAGAAAFgEQAEgGAAgJQAAgKgEgFQgFgEgGAAQgGAAgFAEgAgGgWIgLgNIAMAAIAGANg");
  this.shape_21.setTransform(817.2,112.8);

  this.shape_22 = new cjs.Shape();
  this.shape_22.graphics.f("#333333").s().p("AAZAkIAAg7IgVA7IgHAAIgVg8IAAA8IgJAAIAAhHIAOAAIARAyIADAKIADgLIARgxIANAAIAABHg");
  this.shape_22.setTransform(809.7,112.8);

  this.timeline.addTween(cjs.Tween.get({}).to({state:[{t:this.shape_22},{t:this.shape_21},{t:this.shape_20},{t:this.shape_19},{t:this.shape_18},{t:this.shape_17},{t:this.shape_16},{t:this.shape_15},{t:this.shape_14},{t:this.shape_13},{t:this.shape_12},{t:this.shape_11},{t:this.shape_10},{t:this.shape_9},{t:this.shape_8},{t:this.shape_7},{t:this.shape_6},{t:this.shape_5},{t:this.shape_4},{t:this.shape_3},{t:this.shape_2},{t:this.shape_1},{t:this.shape}]}).wait(9));

  // notice_box
  this.shape_23 = new cjs.Shape();
  this.shape_23.graphics.f().s("#999999").ss(1,1,1).p("AOKAAQAACwkJB9QkKB9l3AAQl3AAkJh9QkJh9AAiwQAAivEJh9QEJh9F3AAQF3AAEKB9QEJB9AACvg");
  this.shape_23.setTransform(871.6,112.6);

  this.shape_24 = new cjs.Shape();
  this.shape_24.graphics.f("#FFFFFF").s().p("AqAEuQkJh+gBiwQABiwEJh8QEKh+F2ABQF3gBEKB+QEJB8ABCwQgBCwkJB+QkKB8l3AAQl2AAkKh8g");
  this.shape_24.setTransform(871.6,112.6);

  this.timeline.addTween(cjs.Tween.get({}).to({state:[{t:this.shape_24},{t:this.shape_23}]}).wait(9));

}).prototype = p_step8 = new cjs.MovieClip();
p_step8.nominalBounds = new cjs.Rectangle(1420,415,289.1,134.3);
// library properties:
lib_step8.properties = {
  id: '9D33D0B2ABC183428F26CA73B2961738_8',
  width: 1280,
  height: 720,
  fps: 24,
  color: "#FFFFFF",
  opacity: 1.00,
  manifest: [
    {src:"/bitrix/components/vportal/vivi_notify/templates/.default/images/happy.png?1533277984985", id:"happy"}
  ],
  preloads: []
};

// bootstrap callback support:

(lib_step8.Stage = function(canvas) {
  createjs.Stage.call(this, canvas);
}).prototype = p_step8 = new createjs.Stage();

p_step8.setAutoPlay = function(autoPlay) {
  this.tickEnabled = autoPlay;
}
p_step8.play = function() { this.tickEnabled = true; this.getChildAt(0).gotoAndPlay(this.getTimelinePosition()) }
p_step8.stop = function(ms) { if(ms) this.seek(ms); this.tickEnabled = false; }
p_step8.seek = function(ms) { this.tickEnabled = true; this.getChildAt(0).gotoAndStop(lib_step8.properties.fps * ms / 1000); }
p_step8.getDuration = function() { return this.getChildAt(0).totalFrames / lib_step8.properties.fps * 1000; }

p_step8.getTimelinePosition = function() { return this.getChildAt(0).currentFrame / lib_step8.properties.fps * 1000; }

//STEP8_END
//============================================================================================
//STEP9_BEGIN

var p_step9; // shortcut to reference prototypes
var lib_step9={};var ss_step9={};var img_stpe9={};
lib_step9.ssMetadata = [];

// symbols:
(lib_step9.happy = function() {
  this.initialize(img_stpe9.happy);
}).prototype = p_step9 = new cjs.Bitmap();
p_step9.nominalBounds = new cjs.Rectangle(0,0,297,302);// helper functions:

function mc_symbol_clone_step9() {
  var clone = this._cloneProps(new this.constructor(this.mode, this.startPosition, this.loop));
  clone.gotoAndStop(this.currentFrame);
  clone.paused = this.paused;
  clone.framerate = this.framerate;
  return clone;
}

function getMCSymbolPrototype_step9(symbol, nominalBounds, frameBounds) {
  var prototype = cjs.extend(symbol, cjs.MovieClip);
  prototype.clone = mc_symbol_clone_step9;
  prototype.nominalBounds = nominalBounds;
  prototype.frameBounds = frameBounds;
  return prototype;
  }

(lib_step9.Symbol1 = function(mode,startPosition,loop) {
  this.initialize(mode,startPosition,loop,{});

  // Layer 1
  this.instance = new lib_step9.happy();
  this.instance.parent = this;
  this.instance.setTransform(0,0,0.445,0.445);

  this.timeline.addTween(cjs.Tween.get(this.instance).wait(1));

}).prototype = getMCSymbolPrototype_step9(lib_step9.Symbol1, new cjs.Rectangle(0,0,132.1,134.3), null);


// stage content:
(lib_step9.notice1_cảnh9_bayđi = function(mode,startPosition,loop) {
  this.initialize(mode,startPosition,false,{});

  // happy
  this.instance = new lib_step9.Symbol1();
  this.instance.parent = this;
  this.instance.setTransform(1003,132.2,1,1,0,0,0,66,67.2);

  this.timeline.addTween(cjs.Tween.get(this.instance).wait(1).to({regY:67.1,scaleX:0.93,scaleY:0.93,x:1022.3,y:129.7},0).wait(1).to({scaleX:0.86,scaleY:0.86,x:1041.2,y:125.6},0).wait(1).to({scaleX:0.79,scaleY:0.79,x:1059.7,y:119.7},0).wait(1).to({scaleX:0.71,scaleY:0.71,x:1077.5,y:111.9},0).wait(1).to({scaleX:0.69,scaleY:0.69,x:1094.4,y:102.3},0).wait(1).to({scaleX:0.67,scaleY:0.67,x:1110.1,y:91},0).wait(1).to({scaleX:0.65,scaleY:0.65,x:1124.6,y:78.1},0).wait(1).to({scaleX:0.63,scaleY:0.63,x:1137.9,y:64},0).wait(1).to({scaleX:0.61,scaleY:0.61,x:1149.8,y:48.7},0).wait(1).to({scaleX:0.52,scaleY:0.52,x:1160.6,y:31.8},0).wait(1).to({scaleX:0.43,scaleY:0.43,x:1168.9,y:13.5},0).wait(1).to({x:1174.2,y:-5.9},0).wait(1).to({x:1175.9,y:-25.8},0).wait(1).to({x:1174,y:-45.8},0).wait(1));

}).prototype = p_step9 = new cjs.MovieClip();
p_step9.nominalBounds = new cjs.Rectangle(1577,425,132,134.3);
// library properties:
lib_step9.properties = {
  id: '9D33D0B2ABC183428F26CA73B2961738_9',
  width: 1280,
  height: 720,
  fps: 15,
  color: "#FFFFFF",
  opacity: 1.00,
  manifest: [
    {src:"/bitrix/components/vportal/vivi_notify/templates/.default/images/happy.png?1533278015625", id:"happy"}
  ],
  preloads: []
};
// bootstrap callback support:
(lib_step9.Stage = function(canvas) {
  createjs.Stage.call(this, canvas);
}).prototype = p_step9 = new createjs.Stage();

p_step9.setAutoPlay = function(autoPlay) {
  this.tickEnabled = autoPlay;
}
p_step9.play = function() { this.tickEnabled = true; this.getChildAt(0).gotoAndPlay(this.getTimelinePosition()) }
p_step9.stop = function(ms) { if(ms) this.seek(ms); this.tickEnabled = false; }
p_step9.seek = function(ms) { this.tickEnabled = true; this.getChildAt(0).gotoAndStop(lib_step9.properties.fps * ms / 1000); }
p_step9.getDuration = function() { return this.getChildAt(0).totalFrames / lib_step9.properties.fps * 1000; }

p_step9.getTimelinePosition = function() { return this.getChildAt(0).currentFrame / lib_step9.properties.fps * 1000; }

//STEP9_END
an.bootcompsLoaded = an.bootcompsLoaded || [];
if(!an.bootstrapListeners) {
  an.bootstrapListeners=[];
}

an.bootstrapCallback=function(fnCallback) {
  an.bootstrapListeners.push(fnCallback);
  if(an.bootcompsLoaded.length > 0) {
    for(var i=0; i<an.bootcompsLoaded.length; ++i) {
      fnCallback(an.bootcompsLoaded[i]);
    }
  }
};

an.compositions = an.compositions || {};
an.compositions['9D33D0B2ABC183428F26CA73B2961738_1'] = {
  getStage: function() { return exportRoot.getStage(); },
  getLibrary: function() { return lib_step1; },
  getSpriteSheet: function() { return ss; },
  getImages: function() { return img_step1; }
};
an.compositions['9D33D0B2ABC183428F26CA73B2961738_2'] = {
  getStage: function() { return exportRoot.getStage(); },
  getLibrary: function() { return lib_step2; },
  getSpriteSheet: function() { return ss_step2; },
  getImages: function() { return img_step2; }
};
an.compositions['9D33D0B2ABC183428F26CA73B2961738_3'] = {
  getStage: function() { return exportRoot.getStage(); },
  getLibrary: function() { return lib_step3; },
  getSpriteSheet: function() { return ss_step3; },
  getImages: function() { return img_step3; }
};
an.compositions['9D33D0B2ABC183428F26CA73B2961738_4'] = {
  getStage: function() { return exportRoot.getStage(); },
  getLibrary: function() { return lib_step4; },
  getSpriteSheet: function() { return ss_step4; },
  getImages: function() { return img_step4; }
};
an.compositions['9D33D0B2ABC183428F26CA73B2961738_4a'] = {
  getStage: function() { return exportRoot.getStage(); },
  getLibrary: function() { return lib_step4a; },
  getSpriteSheet: function() { return ss; },
  getImages: function() { return img_step4a; }
};
an.compositions['9D33D0B2ABC183428F26CA73B2961738_5'] = {
  getStage: function() { return exportRoot.getStage(); },
  getLibrary: function() { return lib_step5; },
  getSpriteSheet: function() { return ss_step5; },
  getImages: function() { return img_step5; }
};
an.compositions['9D33D0B2ABC183428F26CA73B2961738_6'] = {
  getStage: function() { return exportRoot.getStage(); },
  getLibrary: function() { return lib_step6; },
  getSpriteSheet: function() { return ss_step6; },
  getImages: function() { return img_step6; }
};
an.compositions['9D33D0B2ABC183428F26CA73B2961738_7'] = {
  getStage: function() { return exportRoot.getStage(); },
  getLibrary: function() { return lib_step7; },
  getSpriteSheet: function() { return ss_step7; },
  getImages: function() { return img_step7; }
};
an.compositions['9D33D0B2ABC183428F26CA73B2961738_8'] = {
  getStage: function() { return exportRoot.getStage(); },
  getLibrary: function() { return lib_step8; },
  getSpriteSheet: function() { return ss_step8; },
  getImages: function() { return img_step8; }
};
an.compositions['9D33D0B2ABC183428F26CA73B2961738_9'] = {
  getStage: function() { return exportRoot.getStage(); },
  getLibrary: function() { return lib_step9; },
  getSpriteSheet: function() { return ss_step9  ; },
  getImages: function() { return img_stpe9; }
};
an.compositionLoaded = function(id) {
  an.bootcompsLoaded.push(id);
  for(var j=0; j<an.bootstrapListeners.length; j++) {
    an.bootstrapListeners[j](id);
  }
}

an.getComposition = function(id) {

  return an.compositions[id];
}

})(createjs = createjs||{}, AdobeAn = AdobeAn||{});
var createjs, AdobeAn;
</script>

<!-- chungnt: draw con cu --> 
<script type="text/javascript">
//khai bao cac flags
var flag = {
  times_mouseup: 0,
  end: 0,
  step4: 0,
  on_step4: 0,
  on_step6: 0
};
  
//khai bao bien container
var cr_announcement = new createjs.Container();
var ct_announcement = new createjs.DOMElement("announcement");
var cr_previous = new createjs.Container();
var ct_previous = new createjs.DOMElement("previous");
var cr_page_and_page = new createjs.Container();
var ct_page_and_page = new createjs.DOMElement("page_and_page");
var cr_next = new createjs.Container();
var ct_next = new createjs.DOMElement("next");
var cr_mark_as_read = new createjs.Container();
var ct_mark_as_read = new createjs.DOMElement("blog-<?=$uid?>-mark_as_read");
var mark_as_read = document.getElementById("blog-<?=$uid?>-mark_as_read");
var watting = document.getElementById("watting");
var agreement = document.getElementById("agreement");

//lay noi dung cac thong bao moi.
//ghi vao mang trung gian
var arrAnnouncement = [];
var count = 0;

<?if(count($arResult["POST"])>0){
  foreach($arResult["POST"] as $ind => $CurPost){
  ?>
    // arrAnnouncement[count] = <?echo json_encode($CurPost["DETAIL_TEXT"]);?>;
   //    count += 1;
  <?  
  }
}else{
  //TO DO
}?>

//khai bao cac bien mang thong bao
var count_announcement = {
  value : arrAnnouncement.length
};
var index_announcement = {
  value : 1
};

//su kien nhan nut dong y doc
function fnc_agreement(){
  // flag.times_mouseup += 1;
  //if(flag.times_mouseup == 3){
    // flag.times_mouseup = 0;
    stage.removeChild(exportRoot);
    createjs.Ticker.setPaused(false);
    time.step4_begin = createjs.Ticker.getTime();
    // stage.removeEventListener("stagemouseup",arguments.callee);
    createjs.Ticker.removeEventListener("tick", tick);              
    chk = 4;
    init(chk);
  //}
}

//su kien nhan nut cho 10 phut
function fnc_watting(){
  stage.removeChild(exportRoot);
  createjs.Ticker.setPaused(false);
  time.step6_begin = createjs.Ticker.getTime();
  //stage.removeEventListener("stagemouseup",arguments.callee);    
  createjs.Ticker.removeEventListener("tick", tick);
  chk = 6;      
  init(chk);
}
//su kien nhan nut previous
function fnc_previous(){
  if(index_announcement.value == 1){
    //index_announcement.value = count_announcement.value;
  }else{
    index_announcement.value -= 1;
  }
  page_and_page.innerHTML = index_announcement.value + "/" + count_announcement.value;
  announcement.innerHTML = arrAnnouncement[index_announcement.value - 1];
}

//su kien nhan nut next
function fnc_next(){
  if(index_announcement.value == count_announcement.value){
    //index_announcement.value = 1;
  }else{
    index_announcement.value += 1;
  }
  page_and_page.innerText = index_announcement.value + "/" + count_announcement.value;
  announcement.innerHTML = arrAnnouncement[index_announcement.value - 1];
}

//su kien nhan nut next
function fnc_mark_as_book(){
  arrAnnouncement.splice((index_announcement.value - 1), 1);
  count_announcement.value = arrAnnouncement.length;
  if(count_announcement.value == 0){
    stage.removeChild(exportRoot);
    //stage2.removeChild(exportRoot);
    //canvas2.style.zIndex = -1;
    //canvas2.style.display = "none";
    cr_announcement.removeChild();
    cr_page_and_page.removeChild();
    ct_announcement.mouseEnabled = false;
    ct_next.mouseEnabled = false;
    ct_previous.mouseEnabled = false;
    page_and_page.innerText = "";
    announcement.innerHTML = "";
    next.innerHTML = "";
    previous.innerHTML = "";
    time.step5_begin = createjs.Ticker.getTime();
    createjs.Ticker.removeEventListener("tick", tick);  
    chk = 5;
    init(chk);
  }else{
    if(index_announcement.value > count_announcement.value){
      index_announcement.value -= 1;
    }
    page_and_page.innerText = index_announcement.value + "/" + count_announcement.value;
    announcement.innerHTML = arrAnnouncement[index_announcement.value - 1];
  }
}

//phan chay hoat canh
var canvas, stage, canvas2, stage2, exportRoot, anim_container, dom_overlay_container, fnStartAnimation;
var chk = 1;
stage2 = new createjs.Stage("canvas2");
function init(step) {
  canvas = document.getElementById("canvas");
  canvas2 = document.getElementById("canvas2");
  anim_container = document.getElementById("animation_container");
  anim_container.style.zIndex = 99900;
  anim_container.style.display = "block";
  canvas.style.zIndex = 99999;
  canvas.style.display = "block";
  // if(step == 7){
  //   canvas2.style.zIndex = 99999;
  //   canvas2.style.display = "block";
  // }

  dom_overlay_container = document.getElementById("dom_overlay_container");
  switch (step){
    case 1:
      id_step = '9D33D0B2ABC183428F26CA73B2961738_1';
      break;
    case 2:
      id_step = '9D33D0B2ABC183428F26CA73B2961738_2';
      break;
    case 3:
      id_step = '9D33D0B2ABC183428F26CA73B2961738_3';
      break;
    case 4:
        id_step = '9D33D0B2ABC183428F26CA73B2961738_4a';
        break;
      case 5:
        id_step = '9D33D0B2ABC183428F26CA73B2961738_5';
        break;
      case 6:
        id_step = '9D33D0B2ABC183428F26CA73B2961738_6';
        break;
      case 7:
        id_step = '9D33D0B2ABC183428F26CA73B2961738_7';
        break;
      case 8:
        id_step = '9D33D0B2ABC183428F26CA73B2961738_8';
        break;
      default:
        id_step = '9D33D0B2ABC183428F26CA73B2961738_9';
        break;
    }
    var comp=AdobeAn.getComposition(id_step);
    var lib=comp.getLibrary();
    var loader = new createjs.LoadQueue(false);
    loader.addEventListener("fileload", function(evt){handleFileLoad(evt,comp)});
    loader.addEventListener("complete", function(evt){handleComplete(evt,comp,step)});
    loader.loadManifest(lib.properties.manifest);

  }
  function handleFileLoad(evt, comp) {
    var images=comp.getImages();  
    if (evt && (evt.item.type == "image")) { images[evt.item.id] = evt.result; }  
  }
  function handleComplete(evt,comp,step) {
    //This function is always called, irrespective of the content. You can use the variable "stage" after it is created in token create_stage.
    var lib=comp.getLibrary();
    var ss=comp.getSpriteSheet();
    var queue = evt.target;
    var ssMetadata = lib.ssMetadata;
    for(i=0; i<ssMetadata.length; i++) {
      ss[ssMetadata[i].name] = new createjs.SpriteSheet( {"images": [queue.getResult(ssMetadata[i].name)], "frames": ssMetadata[i].frames} )
    }
    switch (step){
      case 1:
        exportRoot = new lib.notice1_cảnh1_xuấthiện();
        break;
      case 2:
        exportRoot = new lib.notice1_cảnh2_hạcánh();
        break;
      case 3:
        exportRoot = new lib.notice1_cảnh3_chàohỏi();
        break;
      case 4:
        exportRoot = new lib.notice1_cảnh4a_đọcthôngbáo();
        break;
      case 5:
        exportRoot = new lib.notice1_cảnh5_tạmbiệt();
        break;
      case 6:
        exportRoot = new lib.notice1_cảnh6_chờ();
        break;
      case 7:
        exportRoot = new lib.notice1_cảnh7_đồnghồchạyngủ();
        break;
      case 8:
        exportRoot = new lib.notice1_cảnh8_mờiđọc();
        break;
      default:
        exportRoot = new lib.notice1_cảnh9_bayđi();
        break;
    }
    // switch (step){
    //   case 1:
    //   case 2:
    //   case 3:
    //   case 4:
    //   case 5:
    //   case 6:
    //   case 9:
        stage = new lib.Stage(canvas);
        stage.addChild(exportRoot);
    //     break;
    //   case 7:
    //   case 8:
    //     stage2 = new lib.Stage(canvas2);
    //     stage2.addChild(exportRoot);
    //     break;
    //   default:
    //     break;
    // }
    //Registers the "tick" event listener.
    //chungnt: xử lý các bước
    fnStartAnimation = function() {
      createjs.Ticker.setFPS(lib.properties.fps);
      tick =  function(){
          if((chk==1)&&((createjs.Ticker.getTime()- time.step1_begin)>time.step1_delay)){
              stage.removeChild(exportRoot);
              time.step2_begin = createjs.Ticker.getTime();
              createjs.Ticker.removeEventListener("tick", tick);
              chk = 2;
              init(chk);
          }else{
            if((chk==2)&&((createjs.Ticker.getTime()-time.step2_begin)>time.step2_delay)){
              stage.removeChild(exportRoot);
              time.step3_begin = createjs.Ticker.getTime();
              createjs.Ticker.removeEventListener("tick", tick);
              chk = 3;
              init(chk);
            }else{
                // if((chk==4)&&((createjs.Ticker.getTime()-time.step4_begin)>time.step4_delay)){
                  // stage.removeChild(exportRoot);
                  // time.step7_begin = createjs.Ticker.getTime();
                  // createjs.Ticker.removeEventListener("tick", tick);
                  // chk = 7;
                  // init(chk);
                // }else{
                  if((chk==5)&&((createjs.Ticker.getTime()-time.step5_begin)>time.step5_delay)){
                    stage.removeChild(exportRoot);
                    time.step9_begin = createjs.Ticker.getTime();
                    createjs.Ticker.removeEventListener("tick", tick);
                    chk = 9;                  
                    init(chk);
                  }else{
                    if((chk==6)&&((createjs.Ticker.getTime()-time.step6_begin)>time.step6_delay)){
                      stage.removeAllChildren();
                      stage.removeAllEventListeners();
                      // stage.canvas = null;
                      createjs.Ticker.setPaused(true);
                      anim_container.style.zIndex = -1;
                      // flag.repeat = 1;
                      // chk = 1;
                      flag.on_step4 = 0;
                      createjs.Ticker.removeEventListener("tick", tick);
                      flag.on_step6 = 1;
                      flag.repeat = setTimeout(function(){
                        if(flag.on_step6 == 1){
                          flag.on_step6 = 0;
                          chk = 1;
                          init(chk);
                        }
                      },600000);
                    }else{
                      // if((chk==7)&&((createjs.Ticker.getTime()-time.step7_begin)>time.step7_delay)){
                        // stage2.removeChild(exportRoot);
                        // stage.removeChild(exportRoot);
                        // time.step8_begin = createjs.Ticker.getTime();
                        // createjs.Ticker.removeEventListener("tick", tick);
                        // chk = 8;
                        // time.step4_begin = createjs.Ticker.getTime();
                        // createjs.Ticker.removeEventListener("tick", tick);
                        // chk = 4;
                        // init(chk);
                      // }else{
                        // if((chk==8)&&((createjs.Ticker.getTime()-time.step8_begin)>time.step8_delay)){
                          // stage2.removeChild(exportRoot);
                          // canvas2.style.zIndex = -1;
                          // canvas2.style.display = "none";
                          // time.step4_begin = createjs.Ticker.getTime();
                          // createjs.Ticker.removeEventListener("tick", tick);
                          // chk = 4;
                          // init(chk);
                        // }else{
                          if((chk==9)&&((createjs.Ticker.getTime()-time.step9_begin)>time.step9_delay)){
                            stage.removeAllChildren();
                            stage.removeAllEventListeners();
                            createjs.Ticker.setPaused(true);
                            anim_container.style.zIndex = -1;
                            // flag.repeat = 0;
                            // flag.end = 1;
                            chk = 1;
                            flag.on_step4 = 0;
                            createjs.Ticker.removeEventListener("tick", tick);
                          }else{
                            //stage2.update();
                            stage.update();
                          }
                        // }
                      // }
                    }
                  }                
                // }
            }
          }
          if(chk == 3){
            watting.style.height = "28px";
            watting.style.width = "150px";
            agreement.style.height = "28px";
            agreement.style.width = "70px";
          }
          //step 4: xu ly hien thi thong bao
          if((chk == 4)&&((createjs.Ticker.getTime()-time.step4_begin)>15)&&(flag.on_step4 == 0)){
            flag.on_step4 = 1;
            //chungnt: noi dung thong bao
            stage.addChild(cr_announcement);
            ct_announcement.mouseEnabled = true;
            announcement.style.display = "block";
            announcement.innerHTML = arrAnnouncement[index_announcement.value - 1];
            cr_announcement.addChild();

            //chungnt: button previous
            stage.addChild(cr_previous);
            ct_previous.mouseEnabled = true;
            previous.innerHTML = "<";
            cr_previous.addChild();

            //chungnt: hien thi trang thong bao (page_and_page)
            stage.addChild(cr_page_and_page);
            page_and_page.innerHTML = index_announcement.value + "/" + count_announcement.value;
            cr_page_and_page.addChild();

            //chungnt: button next
            stage.addChild(cr_next);
            ct_next.mouseEnabled = true;
            next.innerHTML = ">";
            cr_next.addChild();

            //chungnt: button mark_as_read
            stage.addChild(cr_mark_as_read);
            ct_mark_as_read.mouseEnabled = true;
            mark_as_read.style.height = "22px"; 
            mark_as_read.style.width = "110px"; 
            cr_mark_as_read.addChild();
          }
      };
      createjs.Ticker.addEventListener("tick", tick);
    }     

    //Code to support hidpi screens and responsive scaling.
    function makeResponsive(isResp, respDim, isScale, scaleType) {    
      var lastW, lastH, lastS=1;    
      window.addEventListener('resize', resizeCanvas);    
      resizeCanvas();   
      function resizeCanvas() {     
        var w = lib.properties.width, h = lib.properties.height;      
        var iw = window.innerWidth, ih=window.innerHeight;      
        var pRatio = window.devicePixelRatio || 1, xRatio=iw/w, yRatio=ih/h, sRatio=1;      
        if(isResp) {                
          if((respDim=='width'&&lastW==iw) || (respDim=='height'&&lastH==ih)) {                    
            sRatio = lastS;                
          }       
          else if(!isScale) {         
            if(iw<w || ih<h)            
              sRatio = Math.min(xRatio, yRatio);        
          }       
          else if(scaleType==1) {         
            sRatio = Math.min(xRatio, yRatio);        
          }       
          else if(scaleType==2) {         
            sRatio = Math.max(xRatio, yRatio);        
          }     
        }     
        canvas.width = w*pRatio*sRatio;     
        canvas.height = h*pRatio*sRatio;
        canvas.style.width = dom_overlay_container.style.width = anim_container.style.width =  w*sRatio+'px';       
        canvas.style.height = anim_container.style.height = dom_overlay_container.style.height = h*sRatio+'px';
        stage.scaleX = pRatio*sRatio;     
        stage.scaleY = pRatio*sRatio;     
        lastW = iw; lastH = ih; lastS = sRatio;   
      }
    }
    makeResponsive(false,'both',false,1); 
    AdobeAn.compositionLoaded(lib.properties.id);
    fnStartAnimation();
    //chungnt: ham xu ly su kien nhan chuot
    /*stage.addEventListener("stagemouseup", function(){
      //chungnt: sự kiện nhấn nút đồng ý ở bước 3
      if((chk == 3)&&(stage.mouseX >= 830) &&(stage.mouseX <= 887)
      &&(stage.mouseY >= 68)&&(stage.mouseY <= 89)){
        flag.times_mouseup += 1;
        if(flag.times_mouseup == 3){
          flag.times_mouseup = 0;
          stage.removeChild(exportRoot);
          createjs.Ticker.setPaused(false);
          time.step4_begin = createjs.Ticker.getTime();
          stage.removeEventListener("stagemouseup",arguments.callee);
          createjs.Ticker.removeEventListener("tick", tick);              
          chk = 4;
          init(chk);
        }
      }
        
      //chungnt: sự kiện nhấn nút chờ 10 phut moi hien thi lai ở bước 3
      if((chk == 3)&&(stage.mouseX >= 792) &&(stage.mouseX <= 929)
      &&(stage.mouseY >= 108)&&(stage.mouseY <= 128)){
        stage.removeChild(exportRoot);
        createjs.Ticker.setPaused(false);
        time.step6_begin = createjs.Ticker.getTime();
        stage.removeEventListener("stagemouseup",arguments.callee);    
        createjs.Ticker.removeEventListener("tick", tick);
        chk = 6;      
        init(chk);
      }
    });*/
  }

</script>

<?
$filter = $arParams["FILTER"];
// For composite mode
unset($filter["<=DATE_PUBLISH"]);
foreach ($filter as $filterKey => $filterValues)
{
  if (is_numeric($filterKey) && is_array($filterValues))
  {
    foreach ($filterValues as $complexFilterKey => $complexFilterValue)
    {
      if ($complexFilterKey == ">=UF_IMPRTANT_DATE_END")
      {
        unset($filter[$filterKey][$complexFilterKey]);
      }
    }
  }
}
?>

<!-- chugnt: class BSBBW -->
<script type="text/javascript">
;(function(window){
if (top.BSBBW_notify)
  return true;

  function animation(message, main_block){
    if(!BX.browser.isPropertySupported('transform'))
      return false;

    function vendor(props){
      if(BX.browser.isPropertySupported(props))
        return BX.browser.isPropertySupported(props);
      else
        return false
    }

    function getPrefix() {
      var vendorPrefixes = ['moz','webkit', 'o', 'ms'],
        len = vendorPrefixes.length,
        vendor = '';

      while (len--)
        if ('transform' in document.body.style ){
          return vendor
        }else if((vendorPrefixes[len] + 'Transform') in document.body.style){
          vendor='-'+vendorPrefixes[len].toLowerCase()+'-';
        }
      return vendor;
    }

    var corner_gradient = BX.create('div',{
      props:{
        className:'anim-corner-gradient'
      }
    });

    var corner = BX.create('div', { props : { className:'anim-corner' }, children : [ corner_gradient ]}),
      corner_wrap = BX.create('div',{ props:{className:'anim-corner-wrap'}, children:[corner] }),
      distort_shadow = BX.create('div',{ props:{className:'block-distort-shadow-wrap'},
        children:[ BX.create('div',{ props:{className:'block-distort-shadow'} }) ] }),
      distort = BX.create('div', { props:{ className:'block-distort' }, children:[message,corner_wrap] }),
      main_wrap = BX.create('div',{ props:{className:'main-mes-wrap'}, children:[distort, distort_shadow] });

    main_block.appendChild(main_wrap);


    distort.style [vendor('transformOrigin')] = '180px 130px';

    distort.style[vendor('transform')] = 'rotate(42deg)';

    message.style[vendor('transformOrigin')] = '50% 100%';

    message.style[vendor('transformOrigin')] = '50% 100%';
    message.style[vendor('transform')] = 'rotate(-42deg)';

    corner_wrap.style[vendor('transform')] = 'rotate(-42deg)';


    var easing = new BX.easing({
      duration:100,
      start:{
        height:475,
        bottom:-182,
        left:-124,
        shadow_height:0,
        shadow_bottom:-74,
        gradient_height:0,
        gradient_width:0
      },
      finish:{
        height:342,
        bottom:-50,
        left:-72,
        shadow_height:130,
        shadow_bottom:-52,
        gradient_height:172,
        gradient_width:197
      },
      transition : BX.easing.transitions.linear(),
      step:function(state){
        distort.style.height = state.height + 'px';
        corner_wrap.style.left = state.left + 'px';
        corner_wrap.style.bottom = state.bottom + 'px';
        distort_shadow.style.height = state.shadow_height + 'px';
        distort_shadow.style.bottom = state.shadow_bottom + 'px';
        corner_gradient.style.height = state.gradient_height + 'px';
        corner_gradient.style.width = state.gradient_width + 'px';

      },
      complete:function(){

        var gradient_rotate;

        corner_wrap.style[vendor('transformOrigin')] = '62px 0';
        corner_wrap.style.left = -17 + 'px';
        corner_wrap.style.bottom = -183 + 'px';

        distort_shadow.style[vendor('transformOrigin')] = '28px 0';
        distort_shadow.style.left = '-28px';
        distort_shadow.style.bottom = '46px';

        distort.style[vendor('transformOrigin')] = '47px 100%';
        distort.style.top = -195+'px';
        distort.style.left = -46+'px';

        message.style[vendor('transformOrigin')] = '0 0';
        message.style.top = 337 + 'px';
        message.style.left = 41 + 'px';


        var easing_2 = new BX.easing({
          duration:200,
          start:{
            distort_rotate:42,
            shadow_rotate:42,
            shadow_skew:0,
            corner_rotate:-42,
            corner_height:180,
            corner_bottom:-183,
            message_rotate: -42,
            gradient_rotate:42
          },
          finish:{
            distort_rotate:34,
            shadow_rotate:34,
            shadow_skew:11,
            corner_rotate:-50,
            corner_height:251,
            corner_bottom:-248,
            message_rotate: -34,
            gradient_rotate:48
          },
          transition : BX.easing.transitions.linear(),

          step:function(state){

            distort.style[vendor('transform')] = 'rotate('+ state.distort_rotate + 'deg)';

            corner_wrap.style[vendor('transform')] = 'rotate('+ state.corner_rotate + 'deg)';
            corner_wrap.style.bottom = state.corner_bottom + 'px';

            corner.style.height = state.corner_height + 'px';

            distort_shadow.style[vendor('transform')] = 'rotate('+ state.shadow_rotate + 'deg)';

            message.style[vendor('transform')] = 'rotate('+ state.message_rotate + 'deg)';

            corner_gradient.style.height = state.corner_height + 'px';

            corner_gradient.style.backgroundImage = getPrefix()+'linear-gradient('+state.gradient_rotate+'deg, #ece297 42%, #e5d38e 57%, #f6e9a3 78%)';

          },
          complete:function(){

            corner.style[vendor('transformOrigin')] = '100% 0';
            corner.style.boxShadow = 'none';

            if(getPrefix() == '-webkit-') gradient_rotate = 24;
            else gradient_rotate = 67;

            var easing_3 = new BX.easing({
              duration:200,
              start:{
                distort_rotate:34,
                corner_rotate:-50,
                corner_width:260,
                corner_height:251,
                corner_skew:0,
                message_rotate:-34,
                shadow_rotate:34,
                shadow_skew:0,
                shadow_width:340,
                opacity:10,
                gradient_rotate:48,
                gradient_percent:57
              },
              finish:{
                distort_rotate:16,
                corner_rotate:-60,
                corner_width:236,
                corner_height:256,
                corner_skew:8,
                message_rotate:-16,
                shadow_rotate:16,
                shadow_skew:15,
                shadow_width:301,
                opacity:0,
                gradient_rotate:gradient_rotate,
                gradient_percent:50
              },
              transition:BX.easing.transitions.linear(),
              step:function(state){

                distort.style[vendor('transform')] = 'rotate('+ state.distort_rotate + 'deg)';
                distort.style.opacity = (state.opacity/10);

                corner_wrap.style[vendor('transform')] = 'rotate('+ state.corner_rotate + 'deg)';

                corner.style[vendor('transform')] = 'skew('+ state.corner_skew +'deg, 0deg)';
                corner.style.width = state.corner_width + 'px';
                corner.style.height = state.corner_height + 'px';

                corner_gradient.style.height = state.corner_height + 'px';

                message.style[vendor('transform')] = 'rotate('+ state.message_rotate + 'deg)';

                distort_shadow.style[vendor('transform')] = 'rotate('+ state.shadow_rotate + 'deg) skew('+ state.shadow_skew +'deg, 0)';
                distort_shadow.style.width = state.shadow_width + 'px';
                distort_shadow.style.opacity = (state.opacity/10);

                corner_gradient.style.backgroundImage = getPrefix()+'linear-gradient('+state.gradient_rotate+'deg, #ece297 42%, #e5d38e '+state.gradient_percent+'%, #f6e9a3 78%)';
              },
              complete:function(){
                main_wrap.style.display = 'none';
              }
            });
            easing_3.animate()
          }
        });
        easing_2.animate();
      }
    });
    easing.animate();
  }

top.BSBBW_notify = function(params) {
  this.CID = params["CID"];
  this.controller = params["controller"];

  this.nodes = params["nodes"];
  this.tMessage = this.nodes['template'].innerHTML;

  this.url = params["url"];

  this.options = params["options"];
  this.post_info = params["post_info"];
  this.post_info['AJAX_POST'] = "Y";

  this.sended = false;
  this.active = false;
  this.inited = false;
  this.busy = false;
  this.userCounter = 0;

  this.inited = this.init(params);
  this.show();
  this.show_notify();
  BX.addCustomEvent(this.controller, "onDataAppeared", BX.delegate(this.onDataAppeared, this));
  BX.addCustomEvent(this.controller, "onDataRanOut", BX.delegate(this.onDataRanOut, this));
  BX.addCustomEvent(this.controller, "onReachedLimit", BX.delegate(this.onReachedLimit, this));
  BX.addCustomEvent(this.controller, "onRequestSend", BX.delegate(this.showWait, this));
  BX.addCustomEvent(this.controller, "onResponseCame", BX.delegate(this.hideWait, this));
  BX.addCustomEvent(this.controller, "onResponseFailed", BX.delegate(this.hideWait, this));
  BX.addCustomEvent(window, "onImUpdateCounter", BX.delegate(this.onImUpdateCounter, this));
  BX.addCustomEvent("onPullEvent-main", BX.delegate(function(command,params){
    if (command == 'user_counter'
        && params[BX.message('SITE_ID')]
        && params[BX.message('SITE_ID')]["BLOG_POST_IMPORTANT"]
      )
    {
      this.onImUpdateCounter(params[BX.message('SITE_ID')]);
    }
  }, this));
  BX.addCustomEvent(window, 'onSonetLogCounterClear', BX.delegate(function(){this.onImUpdateCounter({"BLOG_POST_IMPORTANT" : 0});}, this));
  BX.addCustomEvent(window, 'onImportantPostRead', BX.delegate(this.onImportantPostRead, this));
}

top.BSBBW_notify.prototype = {
  init : function(params) {
    this.page_settings = params["page_settings"];
    this.page_settings["NavRecordCount"] = parseInt(this.page_settings["NavRecordCount"]);

    this.limit = (this.page_settings["NavPageCount"] > 1 ? 3 : 0);
    this.current = 0;

    if (this.active)
      clearTimeout(this.active);
    this.active = false;

    this.data_id = {};
    this.data = params["data"];
    for (var ii in this.data)
      this.data_id['id' + this.data[ii]["id"]] = 'normal';

    //chungnt: lấy giá trị các record gán vào mảng tạm
    for (var i = 0; i < this.data.length; i++){
      var message = this.tMessage;
      var data = this.data[i];
      for (var ii in data)
        message = message.replace("__" + ii + "__", data[ii]);
      if (data["author_avatar"] !== ""){
        message = message.replace('data-bx-author-avatar="true"', data["author_avatar"]);
      }
      arrAnnouncement[arrAnnouncement.length] = message;
      // console.log(message);
      // var avatar = BX.findChild(this.nodes["announcement"], {attribute : {"data-bx-author-avatar" : true}}, true);
      // if(!!avatar){
      //   console.log("for avatar-------------------2");
      // }
      // if (data["author_avatar_style"] !== "" && !!avatar){

      //   BX.adjust(avatar, {
      //     style: {
      //       backgroundImage: data["author_avatar_style"],
      //       backgroundRepeat: "no-repeat",
      //       backgroundPosition: "center",
      //       backgroundSize: "cover",
      //       backgroundColor: "transparent"
      //     }
      //   });
      // }
    }
    count_announcement.value = arrAnnouncement.length;

    if (this.data.length <= 0)
      BX.onCustomEvent(this.controller, "onDataRanOut");
    else
      BX.onCustomEvent(this.controller, "onDataAppeared");

    if (!this.inited)
    {
      BX.bind(this.nodes["right"], "click", BX.delegate(function(){this.onShiftPage("right")}, this));
      BX.bind(this.nodes["left"], "click", BX.delegate(function(){this.onShiftPage("left")}, this));
      BX.adjust(this.nodes["btn"], {attrs : {url : this.url}, events: {click : BX.delegate(this.onClickToRead, this)}});
      BX.bind(this.nodes["next"], "click", BX.delegate(function(){this.onShiftPage("right")}, this));
      BX.bind(this.nodes["previous"], "click", BX.delegate(function(){this.onShiftPage("left")}, this));
      BX.adjust(this.nodes["mark_as_read"], {attrs : {url : this.url}, events: {click : BX.delegate(this.onClickToRead, this)}});
    }
    return true;
  },
  show_notify : function () {
    if(chk < 4){
      //TO DO
    }else{
      if(chk == 4){
        page_and_page.innerHTML = index_announcement.value + "/" + count_announcement.value;
      }else{
        if((chk == 6) && (flag.on_step6 == 1)){
          flag.on_step6 = 0;
          chk = 1;
          if(!!flag.repeat){
            clearTimeout(flag.repeat);
          }
        }else{
          setTimeout(function(){
            chk = 1;
          },10000);
        }
      }
    }
    if ((count_announcement.value > 0)&&(chk == 1)){
      init(chk);
    }
  },
  show : function() {
    var
      message = this.tMessage,
      data = this.data[this.current];
    if (!data)
      return;
    for (var ii in data)
      message = message.replace("__" + ii + "__", data[ii]);
    this.nodes["leaf"].innerHTML = message;
    this.nodes["leaf"].style.display = "none";
    this.nodes["text"].innerHTML = message;
    this.nodes["text"].style.display = "none";
    this.nodes["counter"].innerHTML = (this.current + 1);
    this.nodes["counter"].style.display = "none";
    this.nodes["total"].innerHTML = this.page_settings["NavRecordCount"];
    this.nodes["total"].style.display = "none";
    var btn = BX.findChild(this.nodes["text"], {"className" : "sidebar-imp-mess-text"}, true);
    var avatar = BX.findChild(this.nodes["text"], {attribute : {"data-bx-author-avatar" : true}}, true);
    if (!!btn)
    {

      BX.adjust(btn, {attrs : {url : this.url}, events: {click : BX.delegate(this.onClickToRead, this)}});
    }

    if (data["author_avatar_style"] !== "" && !!avatar)
    {
      BX.adjust(avatar, {
        style: {
          backgroundImage: data["author_avatar_style"],
          backgroundRepeat: "no-repeat",
          backgroundPosition: "center",
          backgroundSize: "cover",
          backgroundColor: "transparent"
        }
      });
    }

    btn = BX.findChild(this.nodes["leaf"], {"className" : "sidebar-imp-mess-text"}, true);
    avatar = BX.findChild(this.nodes["leaf"], {attribute : {"data-bx-author-avatar" : true}}, true);

    if (data["author_avatar_style"] !== "" && !!avatar)
    {
      BX.adjust(avatar, {
        style: {
          backgroundImage: data["author_avatar_style"],
          backgroundRepeat: "no-repeat",
          backgroundPosition: "center",
          backgroundSize: "cover",
          backgroundColor: "transparent"
        }
      });
    }
  },
  showWait : function() { /* showWait */ },
  hideWait : function() { /* hideWait */ },
  onImUpdateCounter : function(arCount)
  {
    var counter = parseInt(arCount['BLOG_POST_IMPORTANT']);
    if (this.userCounter != counter)
    {
      this.userCounter = counter;
      if (this.userCounter > 0)
      {
        this.startCheck();
      }
    }
  },
  startCheck : function()
  {
    if (this.busy !== true)
    {
      var request = this.post_info;
      request['sessid'] = BX.bitrix_sessid();
      request['page_settings'] = this.page_settings;
      request['page_settings']['iNumPage'] = null;
      BX.ajax({
        'method': 'POST',
        'processData': false,
        'url': this.url,
        'data': request,
        'onsuccess': BX.delegate(function(data){this.busy = false; this.parseResponse(data, true);}, this),
        'onfailure': BX.delegate(function(data){this.busy = false; this.onResponseFailed(data);}, this)
      });
    }
  },
  parseResponse : function(response, fromCheck)
  {
    var data = false, result = false;
    try{eval("result="+ response + ";");} catch(e) {}
    if (!result || !result.data || result.data.length <= 0)
      data = false;
    else if (fromCheck === true)
    {
      var dataNew = [], data = result.data;
      for (var ii in data )
      {
        if (typeof data[ii] == "object" && !this.data_id['id' + data[ii]["id"]])
        {
          dataNew.push(data[ii]);
        }
      }
      result.page_settings["NavRecordCount"] = parseInt(result.page_settings["NavRecordCount"]);
      this.page_settings["NavRecordCount"] = parseInt(this.page_settings["NavRecordCount"]);
      if (this.data.length > 0 &&
        dataNew.length == (result.page_settings["NavRecordCount"] - this.page_settings["NavRecordCount"]))
      {
        var d = dataNew.pop();
        var message;
        while(!!d)
        {
          this.data_id['id' + d["id"]] = 'normal';
          this.data.unshift(d);
          message = this.tMessage;
          for (var ii in d)
            message = message.replace("__" + ii + "__", d[ii]);
          arrAnnouncement.unshift(message);
          index_announcement.value++;
          this.current++;
          d = dataNew.pop();
        }
        this.page_settings["NavPageCount"] = result.page_settings["NavPageCount"];
        this.page_settings["NavRecordCount"] = result.page_settings["NavRecordCount"];
        count_announcement.value = arrAnnouncement.length;
        this.show_notify();
        this.show();
      }
      else
      {
        var current = 0, res = this.data[this.current];
        if (this.data.length > 0 && !!res)
        {
          for (var ii = 0; ii < data.length; ii++)
          {
            if (typeof data[ii] == "object" && data[ii]["id"] == res["id"])
            {
              current = ii;
              break;
            }
          }
        }
        this.init(result);
        this.current = current;
        this.show_notify();
        this.show();
      }
    }
    else
    {
      this.page_settings["NavPageNomer"] = result.page_settings["NavPageNomer"];
      data = result.data;
      for (var ii in data )
      {
        if (typeof data[ii] == "object" && !this.data_id['id' + data[ii]["id"]])
        {
          this.data_id['id' + data[ii]["id"]] = 'normal';
          this.data.push(data[ii]);
        }
      }
      if (this.data.length > 0)
        BX.onCustomEvent(this.controller, "onDataAppeared");
    }
    return true;
  },
  onClickToRead : function(send)
  {
    var
      data = this.data[this.current], options = [], ii;
    for (ii in this.options)
      options.push({post_id : data["id"], name : this.options[ii]['name'], value:this.options[ii]['value']});
    var request = this.post_info;
    request['options'] = options;
    request['page_settings'] = this.page_settings;
    request['sessid'] = BX.bitrix_sessid();
    send = (send === false ? false : true);

    request = BX.ajax.prepareData(request);

    if (send)
    {
      BX.ajax({
        method: 'GET',
        url: this.url + (this.url.indexOf('?') !== -1 ? "&" : "?") + request,
        onsuccess: BX.delegate(this.onAfterClickToRead, this),
        onfailure: function(data){}
      });
    }
    this.onShiftPage('drop');
    animation(this.nodes["leaf"], this.nodes["block"]);
  },
  onAfterClickToRead : function ()
  {
  },
  onShiftPage : function(status)
  {
    if (this.active)
      clearTimeout(this.active);
    this.active = setTimeout(BX.delegate(function(){this.active=false;}, this), 120000);

    if (status == 'drop')
    {
      this.page_settings["NavRecordCount"]--;
      this.data_id['id' + this.data[this.current]["id"]] = 'readed';
      this.data = BX.util.deleteFromArray(this.data, this.current);
      if (!!this.data && this.data.length > 0)
      {
        this.current = this.current - 1;
        status = 'left';
      }
      else
      {
        BX.onCustomEvent(this.controller, "onDataRanOut");
        return;
      }
    }

    if (status == 'right')
    {
      if (this.current <= 0)
      {
        this.page_settings["NavRecordCount"] = parseInt(this.page_settings["NavRecordCount"]);
        if (this.data.length < this.page_settings["NavRecordCount"])
          this.current = 1;
        else
          this.current = this.data.length;
      }
      this.current = this.current - 1;
    }
    else
    {
      if (this.current >= (this.data.length - 1))
        this.current = 0;
      else
        this.current = this.current + 1;
    }
    if (this.limit > 0 && this.current >= (this.data.length - 1 - this.limit))
      BX.onCustomEvent(this.controller, "onReachedLimit");

    this.show();
  },
  onDataRanOut: function()
  {
    if ((!this.data || this.data.length <= 0) && this.controller.style.display != "none")
    {
      this.bodyAnimationheight = this.controller.offsetHeight;
      (this.bodyAnimation = new BX.easing({
        duration : 200,
        start : { height : this.controller.offsetHeight, opacity : 100},
        finish : { height : 0, opacity : 0},
        transition : BX.easing.makeEaseInOut(BX.easing.transitions.quart),
        step : BX.delegate(function(state){
          BX.adjust(this.controller, {style:{height : state.height + 'px', opacity : (state.opacity/100)}});
        }, this),
        complete : BX.delegate(function(){
          this.controller.style.display = "none";
        }, this)
      })).animate();
    }
  },
  onDataAppeared: function()
  {
    if (!!this.data && this.data.length > 0 && this.controller.style.display == "none")
    {
      var height = (!!this.bodyAnimationheight ? this.bodyAnimationheight : 200);
      this.controller.style.display = "block";
      (this.bodyAnimation = new BX.easing({
        duration : 200,
        start : { height : 0, opacity : 0},
        finish : { height : height, opacity : 100},
        transition : BX.easing.makeEaseInOut(BX.easing.transitions.quart),
        step : BX.delegate(function(state){
          BX.adjust(this.controller, {style:{height : state.height + 'px', opacity : (state.opacity/100)}});
        }, this),
        complete : BX.delegate(function(){
          BX.adjust(this.controller, {style:{display : "block", height : "auto", opacity : "auto"}});
        }, this)
      })).animate();
    }
  },
  onReachedLimit : function()
  {
    if (this.sended === true)
      return;

    var
      request = this.post_info,
      needToUnbind = false;

    this.page_settings["NavPageNomer"] = parseInt(this.page_settings["NavPageNomer"]);
    this.page_settings["NavPageCount"] = parseInt(this.page_settings["NavPageCount"]);

    if (this.page_settings["NavPageCount"] <= 1)
      needToUnbind = true;
    else if (this.page_settings["bDescPageNumbering"] == true)
    {
      if (this.page_settings["NavPageNomer"] > 1)
        this.page_settings["iNumPage"] = parseInt(this.page_settings["NavPageNomer"]) - 1;
      else
        needToUnbind = true;
    }
    else if (this.page_settings["NavPageNomer"] < this.page_settings["NavPageCount"])
      this.page_settings["iNumPage"] = parseInt(this.page_settings["NavPageNomer"]) + 1;
    else
      needToUnbind = true;
    if (needToUnbind === true)
    {
      BX.removeCustomEvent(this.controller, "onReachedLimit", BX.delegate(this.onReachedLimit, this));
      return true;
    }
    BX.onCustomEvent(this.controller, "onRequestSend");
    this.sended = true;
    request['page_settings'] = this.page_settings;
    request['sessid'] = BX.bitrix_sessid();
    BX.ajax({
      'method': 'POST',
      'processData': false,
      'url': this.url,
      'data': request,
      'onsuccess': BX.delegate(this.onResponseCame, this),
      'onfailure': BX.delegate(this.onResponseFailed, this)
    });
  },
  onResponseCame : function(data)
  {
    this.sended = false;
    BX.onCustomEvent(this.controller, "onResponseCame");
    this.parseResponse(data);
  },
  onResponseFailed : function(data)
  {
    this.sended = false;
    BX.onCustomEvent(this.controller, "onResponseFailed");
  },
  onImportantPostRead : function(postId, CID)
  {
    if (postId > 0)
    {
      for (var ii in this.data)
      {
        if (this.data[ii]["id"] == postId)
        {
          this.current = ii;
          this.onClickToRead((CID == this.CID));
          break;
        }
      }
    }
  }
}
})(window);
</script>

<script type="text/javascript">
BX.ready(function(){
  if (!!<?=$controller?> && ! <?=$controller?>.loaded)
  {
    <?=$controller?>.loaded = true;
    new BSBBW_notify({
      'CID' : '<?=$uid?>',
      'controller': <?=$controller?>,
      'options' : <?=CUtil::PhpToJSObject($arParams["OPTIONS"])?>,
      'post_info' : {'template' : '<?=$this->__name?>', 'filter' : <?=CUtil::PhpToJSObject($filter)?>, 'avatar_size' : <?=intval($arParams["AVATAR_SIZE"])?>},
      'page_settings' : <?=CUtil::PhpToJSObject($arRes["page_settings"])?>,
      'nodes' : {
        'btn' : BX("blog-<?=$uid?>-btn"),
        'mark_as_read' : BX("blog-<?=$uid?>-mark_as_read"),
        'left' : BX("blog-<?=$uid?>-left"),
        'right' : BX("blog-<?=$uid?>-right"),
        'next' : BX("next"),
        'previous' : BX("previous"),
        'total' : BX("blog-<?=$uid?>-total"),
        'counter' : BX("blog-<?=$uid?>-current"),
        'block' : BX("message-block-<?=$uid?>"),
        'leaf' : BX("blog-leaf-<?=$uid?>"),
        'text' : BX("blog-text-<?=$uid?>"),
        'announcement' : BX("announcement"),
        'template' : BX("blog-<?=$uid?>-template")
      },
      'data' : <?=CUtil::PhpToJSObject($arRes["data"])?>,
      'url' : '<?=CUtil::JSEscape($arResult["urlToPosts"])?>'
    });
  }
  
});

</script>