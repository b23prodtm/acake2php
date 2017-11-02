<!-- usage -->
<?php
/* element cakephp View/Elements/E14/slider.ctp */
//$html = $info->tableauImages($sql, TBL_DIV);
//$slider = $this->r->view->element("E14/slider", 
//                array("photos" => $html,
//                     "maxWidth" => 500,
//                     "maxHeight" => 300),
//                array('cache' => array('config' => 'short', 'key' => 'unique value')));
?>
<!-- #region Jssor Slider Begin -->
<!-- Generator: Jssor Slider Maker -->
<?php 
echo $this->Html->script("/js/jquery-1.11.3.min.js", ["once" => true]); 
echo $this->Html->script("/js/jssor.slider-26.3.0.min.js", ["once" => true]); 
?>
<script type="text/javascript">
		jQuery(document).ready(function ($) {

				var jssor_1_SlideshowTransitions = [
					{$Duration:800,x:0.25,$Zoom:1.5,$Easing:{$Left:$Jease$.$InWave,$Zoom:$Jease$.$InCubic},$Opacity:2,$ZIndex:-10,$Brother:{$Duration:800,x:-0.25,$Zoom:1.5,$Easing:{$Left:$Jease$.$InWave,$Zoom:$Jease$.$InCubic},$Opacity:2,$ZIndex:-10}},
					{$Duration:1200,x:0.5,$Cols:2,$ChessMode:{$Column:3},$Easing:{$Left:$Jease$.$InOutCubic},$Opacity:2,$Brother:{$Duration:1200,$Opacity:2}},
					{$Duration:600,x:0.3,$During:{$Left:[0.6,0.4]},$Easing:{$Left:$Jease$.$InCubic,$Opacity:$Jease$.$Linear},$Opacity:2,$Brother:{$Duration:600,x:-0.3,$Easing:{$Left:$Jease$.$InCubic,$Opacity:$Jease$.$Linear},$Opacity:2}},
					{$Duration:1000,x:1,$Rows:2,$ChessMode:{$Row:3},$Easing:{$Left:$Jease$.$InOutQuart,$Opacity:$Jease$.$Linear},$Opacity:2,$Brother:{$Duration:1000,x:-1,$Rows:2,$ChessMode:{$Row:3},$Easing:{$Left:$Jease$.$InOutQuart,$Opacity:$Jease$.$Linear},$Opacity:2}},
					{$Duration:1000,y:-1,$Cols:2,$ChessMode:{$Column:12},$Easing:{$Top:$Jease$.$InOutQuart,$Opacity:$Jease$.$Linear},$Opacity:2,$Brother:{$Duration:1000,y:1,$Cols:2,$ChessMode:{$Column:12},$Easing:{$Top:$Jease$.$InOutQuart,$Opacity:$Jease$.$Linear},$Opacity:2}},
					{$Duration:800,y:1,$Easing:{$Top:$Jease$.$InOutQuart,$Opacity:$Jease$.$Linear},$Opacity:2,$Brother:{$Duration:800,y:-1,$Easing:{$Top:$Jease$.$InOutQuart,$Opacity:$Jease$.$Linear},$Opacity:2}},
					{$Duration:1000,x:-0.1,y:-0.7,$Rotate:0.1,$During:{$Left:[0.6,0.4],$Top:[0.6,0.4],$Rotate:[0.6,0.4]},$Easing:{$Left:$Jease$.$InQuad,$Top:$Jease$.$InQuad,$Opacity:$Jease$.$Linear,$Rotate:$Jease$.$InQuad},$Opacity:2,$Brother:{$Duration:1000,x:0.2,y:0.5,$Rotate:-0.1,$Easing:{$Left:$Jease$.$InQuad,$Top:$Jease$.$InQuad,$Opacity:$Jease$.$Linear,$Rotate:$Jease$.$InQuad},$Opacity:2}},
					{$Duration:800,x:-0.2,$Delay:40,$Cols:12,$During:{$Left:[0.4,0.6]},$SlideOut:true,$Formation:$JssorSlideshowFormations$.$FormationStraight,$Assembly:260,$Easing:{$Left:$Jease$.$InOutExpo,$Opacity:$Jease$.$InOutQuad},$Opacity:2,$Outside:true,$Round:{$Top:0.5},$Brother:{$Duration:800,x:0.2,$Delay:40,$Cols:12,$Formation:$JssorSlideshowFormations$.$FormationStraight,$Assembly:1028,$Easing:{$Left:$Jease$.$InOutExpo,$Opacity:$Jease$.$InOutQuad},$Opacity:2,$Round:{$Top:0.5},$Shift:-200}},
					{$Duration:700,$Opacity:2,$Brother:{$Duration:700,$Opacity:2}},
					{$Duration:800,x:1,$Easing:{$Left:$Jease$.$InOutQuart,$Opacity:$Jease$.$Linear},$Opacity:2,$Brother:{$Duration:800,x:-1,$Easing:{$Left:$Jease$.$InOutQuart,$Opacity:$Jease$.$Linear},$Opacity:2}},
					{$Duration:800,x:0.25,y:0.5,$Rotate:-0.1,$Easing:{$Left:$Jease$.$InQuad,$Top:$Jease$.$InQuad,$Opacity:$Jease$.$Linear,$Rotate:$Jease$.$InQuad},$Opacity:2,$Brother:{$Duration:800,x:-0.1,y:-0.7,$Rotate:0.1,$Easing:{$Left:$Jease$.$InQuad,$Top:$Jease$.$InQuad,$Opacity:$Jease$.$Linear,$Rotate:$Jease$.$InQuad},$Opacity:2}}
				];

				var jssor_1_options = {
					$AutoPlay: 1,
					$FillMode: 2,
					$Align: 0,
					$SlideshowOptions: {
						$Class: $JssorSlideshowRunner$,
						$Transitions: jssor_1_SlideshowTransitions,
						$TransitionsOrder: 1
					},
					$ArrowNavigatorOptions: {
						$Class: $JssorArrowNavigator$
					}
				};

				var jssor_1_slider = new $JssorSlider$("jssor_1", jssor_1_options);

				//make sure to clear margin of the slider container element
				jssor_1_slider.$Elmt.style.margin = "";

				/*#region responsive code begin*/

				/*
						parameters to scale jssor slider to fill parent container

						MAX_WIDTH
								prevent slider from scaling too wide
						MAX_HEIGHT
								prevent slider from scaling too high, default value is original height
						MAX_BLEEDING
								prevent slider from bleeding outside too much, default value is 1
								0: contain mode, allow up to 0% to bleed outside, the slider will be all inside parent container
								1: cover mode, allow up to 100% to bleed outside, the slider will cover full area of parent container
								0.1: flex mode, allow up to 10% to bleed outside, this is better way to make full window slider, especially for mobile devices
				*/

				var MAX_WIDTH = <?php echo $maxWidth; ?>;
				var MAX_HEIGHT = <?php echo $maxHeight; ?>;
				var MAX_BLEEDING = 0.128;

				function ScaleSlider() {
						var containerElement = jssor_1_slider.$Elmt.parentNode;
						var containerWidth = containerElement.clientWidth;

						if (containerWidth) {
								var originalWidth = jssor_1_slider.$OriginalWidth();
								var originalHeight = jssor_1_slider.$OriginalHeight();

								var containerHeight = containerElement.clientHeight || originalHeight;

								var expectedWidth = Math.min(MAX_WIDTH || containerWidth, containerWidth);
								var expectedHeight = Math.min(MAX_HEIGHT || containerHeight, containerHeight);

								//scale the slider to expected size
								jssor_1_slider.$ScaleSize(expectedWidth, expectedHeight, MAX_BLEEDING);

								//position slider at center in vertical orientation
								jssor_1_slider.$Elmt.style.top = ((containerHeight - expectedHeight) / 2) + "px";

								//position slider at center in horizontal orientation
								jssor_1_slider.$Elmt.style.left = ((containerWidth - expectedWidth) / 2) + "px";
						}
						else {
								window.setTimeout(ScaleSlider, 30);
						}
				}

				ScaleSlider();

				$(window).bind("load", ScaleSlider);
				$(window).bind("resize", ScaleSlider);
				$(window).bind("orientationchange", ScaleSlider);
				/*#endregion responsive code end*/
		});
</script>
<style>
		/* jssor slider loading skin spin css */
		.jssorl-009-spin img {
				animation-name: jssorl-009-spin;
				animation-duration: 1.6s;
				animation-iteration-count: infinite;
				animation-timing-function: linear;
		}

		@keyframes jssorl-009-spin {
				from {
						transform: rotate(0deg);
				}

				to {
						transform: rotate(360deg);
				}
		}


		.jssora105 {display:block;position:absolute;cursor:pointer;}
		.jssora105 .c {fill:#000;opacity:.5;}
		.jssora105 .a {fill:none;stroke:#fff;stroke-width:350;stroke-miterlimit:10;}
		.jssora105:hover .c {opacity:.5;}
		.jssora105:hover .a {opacity:.8;}
		.jssora105.jssora105dn .c {opacity:.2;}
		.jssora105.jssora105dn .a {opacity:1;}
		.jssora105.jssora105ds {opacity:.3;pointer-events:none;}
</style>
<div id="jssor_1" style="position:relative;margin:0 auto;top:0px;left:0px;<?php echo "width:" . $maxWidth . "px;height:" . $maxHeight . "px;"; ?>overflow:hidden;visibility:hidden;">
		<!-- Loading Screen -->
		<div data-u="loading" class="jssorl-009-spin" style="position:absolute;top:0px;left:0px;width:100%;height:100%;text-align:center;background-color:rgba(0,0,0,0.7);">
				<img style="margin-top:-19px;position:relative;top:50%;width:38px;height:38px;" src="/img/spin.svg" />
		</div>
		<div data-u="slides" style="cursor:default;position:relative;top:0px;left:0px;<?php echo "width:" . $maxWidth . "px;height:" . $maxHeight . "px;"; ?>overflow:hidden;"><!--inclure ici les images a afficher comme suit (<div><img data-u="image" src='/images/...'></div>) pour chaque image--><?php
							echo $photos;
							?></div>
		<!-- Arrow Navigator -->
		<div data-u="arrowleft" class="jssora105" style="width:40px;height:50px;top:0px;left:30px;" data-autocenter="2" data-scale="0.75" data-scale-left="0.75">
			<svg viewbox="0 0 16000 16000" style="position:absolute;top:0;left:0;width:100%;height:100%;">
				<circle class="c" cx="8000" cy="8000" r="6260.9"></circle>
				<polyline class="a" points="7930.4,5495.7 5426.1,8000 7930.4,10504.3 "></polyline>
				<line class="a" x1="10573.9" y1="8000" x2="5426.1" y2="8000"></line>
			</svg>
		</div>
		<div data-u="arrowright" class="jssora105" style="width:40px;height:50px;top:0px;right:30px;" data-autocenter="2" data-scale="0.75" data-scale-right="0.75">
			<svg viewbox="0 0 16000 16000" style="position:absolute;top:0;left:0;width:100%;height:100%;">
				<circle class="c" cx="8000" cy="8000" r="6260.9"></circle>
				<polyline class="a" points="8069.6,5495.7 10573.9,8000 8069.6,10504.3 "></polyline>
				<line class="a" x1="5426.1" y1="8000" x2="10573.9" y2="8000"></line>
			</svg>
		</div>
</div>
<!-- #endregion Jssor Slider End -->