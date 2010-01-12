<?php
 /*
     pChart - a PHP class to build charts!
     Copyright (C) 2008 Jean-Damien POGOLOTTI
     Version  1.26e last updated on 07/21/08

     http://pchart.sourceforge.net

     This program is free software: you can redistribute it and/or modify
     it under the terms of the GNU General Public License as published by
     the Free Software Foundation, either version 1,2,3 of the License, or
     (at your option) any later version.

     This program is distributed in the hope that it will be useful,
     but WITHOUT ANY WARRANTY; without even the implied warranty of
     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
     GNU General Public License for more details.

     You should have received a copy of the GNU General Public License
     along with this program.  If not, see <http://www.gnu.org/licenses/>.

     Class initialisation :
      createChart($XSize,$YSize)
     Draw methods :
      drawBackground($R,$G,$B)
      drawRectangle($X1,$Y1,$X2,$Y2,$R,$G,$B)
      drawFilledRectangle($X1,$Y1,$X2,$Y2,$R,$G,$B)
      drawRoundedRectangle($X1,$Y1,$X2,$Y2,$Radius,$R,$G,$B)
      drawFilledRoundedRectangle($X1,$Y1,$X2,$Y2,$Radius,$R,$G,$B)
      drawCircle($Xc,$Yc,$Height,$R,$G,$B,$Width=0)
      drawFilledCircle($Xc,$Yc,$Height,$R,$G,$B,$Width=0)
      drawEllipse($Xc,$Yc,$Height,$Width,$R,$G,$B)
      drawFilledEllipse($Xc,$Yc,$Height,$Width,$R,$G,$B)
      drawLine($X1,$Y1,$X2,$Y2,$R,$G,$B,$GraphFunction=FALSE)
      drawDottedLine($X1,$Y1,$X2,$Y2,$DotSize,$R,$G,$B)
      drawAlphaPixel($X,$Y,$Alpha,$R,$G,$B)
      drawFromPNG($FileName,$X,$Y,$Alpha=100)
      drawFromGIF($FileName,$X,$Y,$Alpha=100)
      drawFromJPG($FileName,$X,$Y,$Alpha=100)
     Graph setup methods :
      setGraphArea($X1,$Y1,$X2,$Y2)
      setFixedScale($VMin,$VMax)
      drawGraphArea($R,$G,$B,$Stripe=FALSE)
      drawScale($Data,$DataDescription,$Divisions,$R,$G,$B,$DrawTicks=TRUE,$Angle=0,$Decimals=1,$WithMargin = FALSE)
      drawGrid($LineWidth,$Mosaic=TRUE,$R=220,$G=220,$B=220,$Alpha=100)
      drawLegend($XPos,$YPos,$DataDescription,$R,$G,$B)
      drawPieLegend($XPos,$YPos,$Data,$DataDescription,$R,$G,$B)
      drawTitle($XPos,$YPos,$Value,$R,$G,$B,$XPos2 = -1, $YPos2 = -1)
      drawTreshold($Value,$R,$G,$B,$ShowLabel=FALSE,$ShowOnRight=FALSE,$TickWidth=4)
      setLabel($Data,$DataDescription,$SerieName,$ValueName,$Caption,$R=210,$G=210,$B=210)
      drawArea($Data,$Serie1,$Serie2,$R,$G,$B,$Alpha = 50)
      drawRadarAxis($Data,$DataDescription,$Mosaic=TRUE,$BorderOffset=10,$A_R=60,$A_G=60,$A_B=60,$S_R=200,$S_G=200,$S_B=200,$MaxValue=-1)
      setColorPalette($ID,$R,$G,$B)
      loadColorPalette($FileName,$Delimiter=",")
      writeValues($Data,$DataDescription,$Series)
     Graphs methods :
      drawPlotGraph($Data,$DataDescription,$BigRadius=5,$SmallRadius=2,$R2=-1,$G2=-1,$B2=-1)
      drawLineGraph($Data,$DataDescription)
      drawFilledLineGraph($Data,$DataDescription,$Alpha=100,$AroundZero=FALSE)
      drawCubicCurve($Data,$DataDescription,$Accuracy)
      drawFilledCubicCurve($Data,$DataDescription,$Accuracy,$Alpha=100,$AroundZero=FALSE)
      drawLimitsGraph($Data,$DataDescription,$R=0,$G=0,$B=0)
      drawBarGraph($Data,$DataDescription,$Shadow=FALSE)
      drawOverlayBarGraph($Data,$DataDescription,$Alpha=50)
      drawRadar($Data,$DataDescription,$MaxValue=-1)
      drawFilledRadar($Data,$DataDescription,$Alpha=50,$MaxValue=-1)
      drawPieGraph($Data,$DataDescription,$XPos,$YPos,$Radius=100,$DrawLabels=TRUE,$EnhanceColors=TRUE,$Skew=60,$SpliceHeight=20,$SpliceDistance=0,$Decimals=0)
      drawFlatPieGraph($Data,$DataDescription,$XPos,$YPos,$Radius=100,$DrawLabels=TRUE,$SpliceDistance=0,$Decimals=0)
     Other methods :
      getLegendBox($DataDescription)
      setFontProperties($FontName,$FontSize)
      Render($FileName)
      Stroke()
 */
 
 /* pChart class definition */
 class pChart
  {
   /* Palettes definition */
   var $Palette = array("0"=>array("R"=>188,"G"=>224,"B"=>46),
                        "1"=>array("R"=>224,"G"=>100,"B"=>46),
                        "2"=>array("R"=>224,"G"=>214,"B"=>46),
                        "3"=>array("R"=>46,"G"=>151,"B"=>224),
                        "4"=>array("R"=>176,"G"=>46,"B"=>224),
                        "5"=>array("R"=>224,"G"=>46,"B"=>117),
                        "6"=>array("R"=>92,"G"=>224,"B"=>46),
                        "7"=>array("R"=>224,"G"=>176,"B"=>46));

   /* Some static vars used in the class */
   var $XSize          = NULL;
   var $YSize          = NULL;
   var $Picture        = NULL;

   /* vars related to the graphing area */
   var $GArea_X1       = NULL;
   var $GArea_Y1       = NULL;
   var $GArea_X2       = NULL;
   var $GArea_Y2       = NULL;
   var $GAreaXOffset   = NULL;
   var $VMax           = NULL;
   var $VMin           = NULL;
   var $DivisionHeight = NULL;
   var $DivisionCount  = NULL;
   var $DivisionRatio  = NULL;
   var $DivisionWidth  = NULL;
   var $DataCount      = NULL;

   /* Text format related vars */
   var $FontName       = NULL;
   var $FontSize       = NULL;

   /* Lines format related vars */
   var $LineWidth      = 1;
   var $LineDotSize    = 0;

   /* Layer related vars */
   var $Layers         = NULL;

   /* Set antialias quality : 0 is maximum, 100 minimum*/
   var $AntialiasQuality = 10;

   /* This function create the background picture */
   function pChart($XSize,$YSize)
    {
     $this->XSize   = $XSize;
     $this->YSize   = $YSize;
     $this->Picture = imagecreatetruecolor($XSize,$YSize);

     $C_White = imagecolorallocate($this->Picture,255,255,255);
     imagefilledrectangle($this->Picture,0,0,$XSize,$YSize,$C_White);
     imagecolortransparent($this->Picture,$C_White);

     $this->setFontProperties("tahoma.ttf",8);
    }

   /* Set the font properties */
   function setFontProperties($FontName,$FontSize)
    {
     $this->FontName = $FontName;
     $this->FontSize = $FontSize;
    }

   /* Set Palette color */
   function setColorPalette($ID,$R,$G,$B)
    {
     if ( $R < 0 ) { $R = 0; } if ( $R > 255 ) { $R = 255; }
     if ( $G < 0 ) { $G = 0; } if ( $G > 255 ) { $G = 255; }
     if ( $B < 0 ) { $B = 0; } if ( $B > 255 ) { $B = 255; }

     $this->Palette[$ID]["R"] = $R;
     $this->Palette[$ID]["G"] = $G;
     $this->Palette[$ID]["B"] = $B;
    }

   /* Load Color Palette from file */
   function loadColorPalette($FileName,$Delimiter=",")
    {
     $handle  = @fopen($FileName,"r");
     $ColorID = 0;
     if ($handle)
      {
       while (!feof($handle))
        {
         $buffer = fgets($handle, 4096);
         $buffer = str_replace(chr(10),"",$buffer);
         $buffer = str_replace(chr(13),"",$buffer);
         $Values = split($Delimiter,$buffer);
         if ( count($Values) == 3 )
          {
           $this->Palette[$ColorID]["R"] = $Values[0];
           $this->Palette[$ColorID]["G"] = $Values[1];
           $this->Palette[$ColorID]["B"] = $Values[2];
           $ColorID++;
          }
        }
      }
    }

   /* Set line style */
  function setLineStyle($Width=1,$DotSize=0)
   {
    $this->LineWidth   = $Width;
    $this->LineDotSize = $DotSize;
   }

   /* Set the graph area location */
   function setGraphArea($X1,$Y1,$X2,$Y2)
    {
     $this->GArea_X1 = $X1;
     $this->GArea_Y1 = $Y1;
     $this->GArea_X2 = $X2;
     $this->GArea_Y2 = $Y2;
    }

   /* Prepare the graph area */
   function drawGraphArea($R,$G,$B,$Stripe=FALSE)
    {
     $this->drawFilledRectangle($this->GArea_X1,$this->GArea_Y1,$this->GArea_X2,$this->GArea_Y2,$R,$G,$B,FALSE);
     $this->drawRectangle($this->GArea_X1,$this->GArea_Y1,$this->GArea_X2,$this->GArea_Y2,$R-40,$G-40,$B-40);

     if ( $Stripe )
      {
       $R2 = $R-15; if ( $R2 < 0 ) { $R2 = 0; }
       $G2 = $R-15; if ( $G2 < 0 ) { $G2 = 0; }
       $B2 = $R-15; if ( $B2 < 0 ) { $B2 = 0; }

       $LineColor = imagecolorallocate($this->Picture,$R2,$G2,$B2);
       $SkewWidth = ($this->GArea_Y2-$this->GArea_Y1-1);

       for($i=$this->GArea_X1-$SkewWidth;$i<=$this->GArea_X2;$i=$i+4)
        {
         $X1 = $i;            $Y1 = $this->GArea_Y2;
         $X2 = $i+$SkewWidth; $Y2 = $this->GArea_Y1;

         if ( $X1 < $this->GArea_X1 )
          { $X1 = $this->GArea_X1; $Y1 = $this->GArea_Y1 + $X2 - $this->GArea_X1 + 1; }

         if ( $X2 >= $this->GArea_X2 )
          { $X2 = $this->GArea_X2 - 1; $Y2 = $this->GArea_Y2 - ($this->GArea_X2 - $X1); }

         imageline($this->Picture,$X1,$Y1,$X2,$Y2+1,$LineColor);
        }
      }
    }

   /* Allow you to fix the scale */
   function setFixedScale($VMin,$VMax)
    {
     $this->VMin = $VMin;
     $this->VMax = $VMax;
    }

   /* Compute and draw the scale */
   function drawScale($Data,$DataDescription,$Divisions,$R,$G,$B,$DrawTicks=TRUE,$Angle = 0,$Decimals = 1,$WithMargin = FALSE,$SkipLabels=1)
    {
     $C_TextColor         = imagecolorallocate($this->Picture,$R,$G,$B);
     $this->DivisionCount = $Divisions;

     $this->drawLine($this->GArea_X1,$this->GArea_Y1,$this->GArea_X1,$this->GArea_Y2,$R,$G,$B);
     $this->drawLine($this->GArea_X1,$this->GArea_Y2,$this->GArea_X2,$this->GArea_Y2,$R,$G,$B);

     if ( $this->VMin == NULL && $this->VMax == NULL)
      {
       /* Vertical Axis */
       $this->VMin = $Data[0][$DataDescription["Values"][0]];
       $this->VMax = $Data[0][$DataDescription["Values"][0]];
       foreach ( $Data as $Key => $Values )
        {
         foreach ( $DataDescription["Values"] as $Key2 => $ColName )
          {
           if (isset($Data[$Key][$ColName]))
            {
             $Value = $Data[$Key][$ColName];

             if ( $Value > $this->VMax ) { $this->VMax = $Value; }
             if ( $Value < $this->VMin ) { $this->VMin = $Value; }
            }
          }
        }

       $DataRange = $this->VMax - $this->VMin;
       if ( $DataRange == 0 ) { $DataRange = .1; }

       $ScaleOffset = $DataRange / 10;   
       $this->VMax  = $this->VMax + $ScaleOffset;
       $this->VMin  = $this->VMin - $ScaleOffset;
      }

     $DataRange = $this->VMax - $this->VMin;
     if ( $DataRange == 0 ) { $DataRange = .1; }

     $this->DivisionHeight = ( $this->GArea_Y2 - $this->GArea_Y1 ) / $Divisions;
     $this->DivisionRatio  = ( $this->GArea_Y2 - $this->GArea_Y1 ) / $DataRange;

     $this->GAreaXOffset  = 0;
     if ( count($Data) > 1 )
      {
       if ( $WithMargin == FALSE )
        $this->DivisionWidth = ( $this->GArea_X2 - $this->GArea_X1 ) / (count($Data)-1);
       else
        {
         $this->DivisionWidth = ( $this->GArea_X2 - $this->GArea_X1 ) / (count($Data));
         $this->GAreaXOffset  = $this->DivisionWidth / 2;
        }
      }
     else
      $this->DivisionWidth = $this->GArea_X2 - $this->GArea_X1;

     $this->DataCount = count($Data);

     if ( $DrawTicks == FALSE )
      return(0);

     $YPos = $this->GArea_Y2;
     for($i=1;$i<=$Divisions+1;$i++)
      {
       $this->drawLine($this->GArea_X1,$YPos,$this->GArea_X1-5,$YPos,$R,$G,$B);
       $Value     = $this->VMin + ($i-1) * (( $this->VMax - $this->VMin ) / $Divisions);
       $Value     = floor($Value * pow(10,$Decimals)) / pow(10,$Decimals);
       $Position  = imageftbbox($this->FontSize,0,$this->FontName,$Value);
       $TextWidth = $Position[2]-$Position[0];
       imagettftext($this->Picture,$this->FontSize,0,$this->GArea_X1-10-$TextWidth,$YPos+($this->FontSize/2),$C_TextColor,$this->FontName,$Value);

       $YPos = $YPos - $this->DivisionHeight;
      }

     /* Horizontal Axis */
     $XPos = $this->GArea_X1 + $this->GAreaXOffset;
     if ( count($Data) <= 1 ) { return(-1); }
     $ID = 1;
     foreach ( $Data as $Key => $Values )
      {
       if ( $ID % $SkipLabels == 0 )
        {
         $this->drawLine(floor($XPos),$this->GArea_Y2,floor($XPos),$this->GArea_Y2+5,$R,$G,$B);
         $Value = $Data[$Key][$DataDescription["Position"]];
         $Position  = imageftbbox($this->FontSize,$Angle,$this->FontName,$Value);
         $TextWidth = abs($Position[2])+abs($Position[0]);
         if ( $Angle == 0 )
          imagettftext($this->Picture,$this->FontSize,$Angle,floor($XPos)-floor($TextWidth/2),$this->GArea_Y2+18,$C_TextColor,$this->FontName,$Value);
         else
          {
           $TextHeight = abs($Position[1])+abs($Position[3]);
           if ( $Angle <= 90 )
            imagettftext($this->Picture,$this->FontSize,$Angle,floor($XPos)-$TextWidth+5,$this->GArea_Y2+10+$TextHeight,$C_TextColor,$this->FontName,$Value);
           else
            imagettftext($this->Picture,$this->FontSize,$Angle,floor($XPos)+$TextWidth+5,$this->GArea_Y2+10+$TextHeight,$C_TextColor,$this->FontName,$Value);
          }
        }

       $XPos = $XPos + $this->DivisionWidth;
       $ID++;
      }
    }

   /* Compute and draw the scale */
   function drawGrid($LineWidth,$Mosaic=TRUE,$R=220,$G=220,$B=220,$Alpha=100)
    {
     /* Draw mosaic */
     if ( $Mosaic )
      {
       $LayerWidth  = $this->GArea_X2-$this->GArea_X1;
       $LayerHeight = $this->GArea_Y2-$this->GArea_Y1;

       $this->Layers[0] = imagecreatetruecolor($LayerWidth,$LayerHeight);
       $C_White         = imagecolorallocate($this->Layers[0],255,255,255);
       imagefilledrectangle($this->Layers[0],0,0,$LayerWidth,$LayerHeight,$C_White);
       imagecolortransparent($this->Layers[0],$C_White);

       $C_Rectangle = imagecolorallocate($this->Layers[0],250,250,250);

       $YPos  = $LayerHeight; //$this->GArea_Y2-1;
       $LastY = $YPos;
       for($i=0;$i<=$this->DivisionCount;$i++)
        {
         $LastY = $YPos;
         $YPos  = $YPos - $this->DivisionHeight;

         if ( $YPos <= 0 ) { $YPos = 1; }

         if ( $i % 2 == 0 )
          {
           imagefilledrectangle($this->Layers[0],1,$YPos,$LayerWidth-1,$LastY,$C_Rectangle);
          }
        }
       imagecopymerge($this->Picture,$this->Layers[0],$this->GArea_X1,$this->GArea_Y1,0,0,$LayerWidth,$LayerHeight,$Alpha);
       imagedestroy($this->Layers[0]);
      }

     /* Horizontal lines */
     $YPos = $this->GArea_Y2 - $this->DivisionHeight;
     for($i=1;$i<=$this->DivisionCount;$i++)
      {
       if ( $YPos > $this->GArea_Y1 && $YPos < $this->GArea_Y2 )
        $this->drawDottedLine($this->GArea_X1,$YPos,$this->GArea_X2,$YPos,$LineWidth,$R,$G,$B);
        
       $YPos = $YPos - $this->DivisionHeight;
      }

     /* Vertical lines */
     if ( $this->GAreaXOffset == 0 )
      { $XPos = $this->GArea_X1 + $this->DivisionWidth + $this->GAreaXOffset; $ColCount = $this->DataCount-2; }
     else
      { $XPos = $this->GArea_X1 + $this->GAreaXOffset; $ColCount = $this->DataCount; }

     for($i=1;$i<=$ColCount;$i++)
      {
       if ( $XPos > $this->GArea_X1 && $XPos < $this->GArea_X2 )
        $this->drawDottedLine(floor($XPos),$this->GArea_Y1,floor($XPos),$this->GArea_Y2,$LineWidth,$R,$G,$B);
       $XPos = $XPos + $this->DivisionWidth;
      }
    }


   /* Compute and draw the scale */
   function drawGrid_deprecated($LineWidth,$Mosaic=TRUE,$R=220,$G=220,$B=220)
    {
     /* Draw mosaic */
     if ( $Mosaic )
      {
       $C_Rectangle = imagecolorallocate($this->Picture,250,250,250);

       $YPos  = $this->GArea_Y2-1;
       $LastY = $YPos;
       for($i=0;$i<=$this->DivisionCount;$i++)
        {
         $LastY = $YPos;
         $YPos  = $YPos - $this->DivisionHeight;

         if ( $YPos <= $this->GArea_Y1 ) { $YPos = $this->GArea_Y1+1; }

         if ( $i % 2 == 0 )
          {
           imagefilledrectangle($this->Picture,$this->GArea_X1+1,$YPos,$this->GArea_X2-1,$LastY,$C_Rectangle);
          }
        }
      }

     /* Horizontal lines */
     $YPos = $this->GArea_Y2 - $this->DivisionHeight;
     for($i=1;$i<=$this->DivisionCount;$i++)
      {
       if ( $YPos > $this->GArea_Y1 && $YPos < $this->GArea_Y2 )
        $this->drawDottedLine($this->GArea_X1,$YPos,$this->GArea_X2,$YPos,$LineWidth,$R,$G,$B);
        
       $YPos = $YPos - $this->DivisionHeight;
      }

     /* Vertical lines */
     if ( $this->GAreaXOffset == 0 )
      { $XPos = $this->GArea_X1 + $this->DivisionWidth + $this->GAreaXOffset; $ColCount = $this->DataCount-2; }
     else
      { $XPos = $this->GArea_X1 + $this->GAreaXOffset; $ColCount = $this->DataCount; }

     for($i=1;$i<=$ColCount;$i++)
      {
       if ( $XPos > $this->GArea_X1 && $XPos < $this->GArea_X2 )
        $this->drawDottedLine(floor($XPos),$this->GArea_Y1,floor($XPos),$this->GArea_Y2,$LineWidth,$R,$G,$B);
       $XPos = $XPos + $this->DivisionWidth;
      }
    }

   /* retrieve the legends size */
   function getLegendBox($DataDescription)
    {
     if ( !isset($DataDescription["Description"]) )
      return(-1);

     /* <-10->[8]<-4->Text<-10-> */
     $MaxWidth = 0; $MaxHeight = 8;
     foreach($DataDescription["Description"] as $Key => $Value)
      {
       $Position  = imageftbbox($this->FontSize,0,$this->FontName,$Value);
       $TextWidth = $Position[2]-$Position[0];
       if ( $TextWidth > $MaxWidth) { $MaxWidth = $TextWidth; }
       $MaxHeight = $MaxHeight + ( $this->FontSize + 6 );
      }
     $MaxHeight = $MaxHeight - 3;
     $MaxWidth  = $MaxWidth + 32;

     return(array($MaxWidth,$MaxHeight));
    }

   /* Draw the data legends */
   function drawLegend($XPos,$YPos,$DataDescription,$R,$G,$B)
    {
     if ( !isset($DataDescription["Description"]) )
      return(-1);

     $C_TextColor = imagecolorallocate($this->Picture,0,0,0);

     /* <-10->[8]<-4->Text<-10-> */
     $MaxWidth = 0; $MaxHeight = 8;
     foreach($DataDescription["Description"] as $Key => $Value)
      {
       $Position  = imageftbbox($this->FontSize,0,$this->FontName,$Value);
       $TextWidth = $Position[2]-$Position[0];
       if ( $TextWidth > $MaxWidth) { $MaxWidth = $TextWidth; }
       $MaxHeight = $MaxHeight + ( $this->FontSize + 6 );
      }
     $MaxHeight = $MaxHeight - 3;
     $MaxWidth  = $MaxWidth + 32;

     $this->drawFilledRoundedRectangle($XPos+1,$YPos+1,$XPos+$MaxWidth+1,$YPos+$MaxHeight+1,5,$R-30,$G-30,$B-30);
     $this->drawFilledRoundedRectangle($XPos,$YPos,$XPos+$MaxWidth,$YPos+$MaxHeight,5,$R,$G,$B);

     $YOffset = 4 + $this->FontSize; $ID = 0;
     foreach($DataDescription["Description"] as $Key => $Value)
      {
       $this->drawFilledRoundedRectangle($XPos+10,$YPos+$YOffset-4,$XPos+14,$YPos+$YOffset-4,2,$this->Palette[$ID]["R"],$this->Palette[$ID]["G"],$this->Palette[$ID]["B"]);

       imagettftext($this->Picture,$this->FontSize,0,$XPos+22,$YPos+$YOffset,$C_TextColor,$this->FontName,$Value);
       $YOffset = $YOffset + ( $this->FontSize + 6 );
       $ID++;
      }
    }

   /* Draw the data legends */
   function drawPieLegend($XPos,$YPos,$Data,$DataDescription,$R,$G,$B)
    {
     if ( !isset($DataDescription["Position"]) )
      return(-1);

     $C_TextColor = imagecolorallocate($this->Picture,0,0,0);

     /* <-10->[8]<-4->Text<-10-> */
     $MaxWidth = 0; $MaxHeight = 8;
     foreach($Data as $Key => $Value)
      {
       $Value2 = $Value[$DataDescription["Position"]];
       $Position  = imageftbbox($this->FontSize,0,$this->FontName,$Value2);
       $TextWidth = $Position[2]-$Position[0];
       if ( $TextWidth > $MaxWidth) { $MaxWidth = $TextWidth; }
       $MaxHeight = $MaxHeight + ( $this->FontSize + 6 );
      }
     $MaxHeight = $MaxHeight - 3;
     $MaxWidth  = $MaxWidth + 32;

     $this->drawFilledRoundedRectangle($XPos+1,$YPos+1,$XPos+$MaxWidth+1,$YPos+$MaxHeight+1,5,$R-30,$G-30,$B-30);
     $this->drawFilledRoundedRectangle($XPos,$YPos,$XPos+$MaxWidth,$YPos+$MaxHeight,5,$R,$G,$B);

     $YOffset = 4 + $this->FontSize; $ID = 0;
     foreach($Data as $Key => $Value)
      {
       $Value2 = $Value[$DataDescription["Position"]];

       $this->drawFilledRectangle($XPos+10,$YPos+$YOffset-6,$XPos+14,$YPos+$YOffset-2,$this->Palette[$ID]["R"],$this->Palette[$ID]["G"],$this->Palette[$ID]["B"]);

       imagettftext($this->Picture,$this->FontSize,0,$XPos+22,$YPos+$YOffset,$C_TextColor,$this->FontName,$Value2);
       $YOffset = $YOffset + ( $this->FontSize + 6 );
       $ID++;
      }
    }

   /* Draw the graph title */
   function drawTitle($XPos,$YPos,$Value,$R,$G,$B,$XPos2 = -1, $YPos2 = -1)
    {
     $C_TextColor = imagecolorallocate($this->Picture,$R,$G,$B);

     if ( $XPos2 != -1 )
      {
       $Position  = imageftbbox($this->FontSize,0,$this->FontName,$Value);
       $TextWidth = $Position[2]-$Position[0];
       $XPos      = floor(( $XPos2 - $XPos - $TextWidth ) / 2 ) + $XPos;
      }

     if ( $YPos2 != -1 )
      {
       $Position  = imageftbbox($this->FontSize,0,$this->FontName,$Value);
       $TextHeight = $Position[5]-$Position[3];
       $YPos      = floor(( $YPos2 - $YPos - $TextHeight ) / 2 ) + $YPos;
      }

     imagettftext($this->Picture,$this->FontSize,0,$XPos,$YPos,$C_TextColor,$this->FontName,$Value);     
    }

   /* Compute and draw the scale */
   function drawTreshold($Value,$R,$G,$B,$ShowLabel=FALSE,$ShowOnRight=FALSE,$TickWidth=4)
    {
     if ( $R < 0 ) { $R = 0; } if ( $R > 255 ) { $R = 255; }
     if ( $G < 0 ) { $G = 0; } if ( $G > 255 ) { $G = 255; }
     if ( $B < 0 ) { $B = 0; } if ( $B > 255 ) { $B = 255; }

     $C_TextColor = imagecolorallocate($this->Picture,$R,$G,$B);
     $Y = $this->GArea_Y2 - ($Value - $this->VMin) * $this->DivisionRatio;

     if ( $Y <= $this->GArea_Y1 || $Y >= $this->GArea_Y2 )
      return(-1);

     if ( $TickWidth == 0 )
      $this->drawLine($this->GArea_X1,$Y,$this->GArea_X2,$Y,$R,$G,$B);
     else
      $this->drawDottedLine($this->GArea_X1,$Y,$this->GArea_X2,$Y,$TickWidth,$R,$G,$B);

     if ( $ShowLabel )
      {
       if ( $ShowOnRight )
        imagettftext($this->Picture,$this->FontSize,0,$this->GArea_X2+2,$Y+($this->FontSize/2),$C_TextColor,$this->FontName,$Value);
       else
        imagettftext($this->Picture,$this->FontSize,0,$this->GArea_X1+2,$Y-($this->FontSize/2),$C_TextColor,$this->FontName,$Value);
      }
    }

   /* This function put a label on a specific point */
   function setLabel($Data,$DataDescription,$SerieName,$ValueName,$Caption,$R=210,$G=210,$B=210)
    {
     $C_Label     = imagecolorallocate($this->Picture,$R,$G,$B);
     $C_Shadow    = imagecolorallocate($this->Picture,$R-30,$G-30,$B-30);
     $C_TextColor = imagecolorallocate($this->Picture,0,0,0);

     $Cp = 0; $Found = FALSE;
     foreach ( $Data as $Key => $Value )
      {
       if ( $Data[$Key][$DataDescription["Position"]] == $ValueName )
        { $NumericalValue = $Data[$Key][$SerieName]; $Found = TRUE; }
       if ( !$Found )
        $Cp++;
      }

     $XPos = $this->GArea_X1 + $this->GAreaXOffset + ( $this->DivisionWidth * $Cp ) + 2;
     $YPos = $this->GArea_Y2 - ($NumericalValue - $this->VMin) * $this->DivisionRatio;

     $Position   = imageftbbox($this->FontSize,0,$this->FontName,$Caption);
     $TextHeight = $Position[3] - $Position[5];
     $TextWidth = $Position[2]-$Position[0];
     $TextOffset = floor($TextHeight/2);

     // Shadow
     $Poly = array($XPos+1,$YPos+1,$XPos + 9,$YPos - $TextOffset,$XPos + 8,$YPos + $TextOffset + 2);
     imagefilledpolygon($this->Picture,$Poly,3,$C_Shadow);
     $this->drawLine($XPos,$YPos+1,$XPos + 9,$YPos - $TextOffset - 1,$R-30,$G-30,$B-30);
     $this->drawLine($XPos,$YPos+1,$XPos + 9,$YPos + $TextOffset + 3,$R-30,$G-30,$B-30);
     $this->drawFilledRectangle($XPos + 9,$YPos - $TextOffset,$XPos + 13 + $TextWidth,$YPos + $TextOffset + 2,$R-30,$G-30,$B-30);

     // Label background
     $Poly = array($XPos,$YPos,$XPos + 8,$YPos - $TextOffset - 1,$XPos + 8,$YPos + $TextOffset + 1);
     imagefilledpolygon($this->Picture,$Poly,3,$C_Label);
     $this->drawLine($XPos-1,$YPos,$XPos + 8,$YPos - $TextOffset - 2,$R,$G,$B);
     $this->drawLine($XPos-1,$YPos,$XPos + 8,$YPos + $TextOffset + 2,$R,$G,$B);
     $this->drawFilledRectangle($XPos + 8,$YPos - $TextOffset - 1,$XPos + 12 + $TextWidth,$YPos + $TextOffset + 1,$R,$G,$B);

     imagettftext($this->Picture,$this->FontSize,0,$XPos + 10,$YPos + $TextOffset,$C_TextColor,$this->FontName,$Caption);
    }

   /* This function draw a line graph */
   function drawPlotGraph($Data,$DataDescription,$BigRadius=5,$SmallRadius=2,$R2=-1,$G2=-1,$B2=-1)
    {
     $GraphID = 0;

     foreach ( $DataDescription["Values"] as $Key2 => $ColName )
      {
       $ID = 0;
       foreach ( $DataDescription["Description"] as $keyI => $ValueI )
        { if ( $keyI == $ColName ) { $ColorID = $ID; }; $ID++; }

       $XPos   = $this->GArea_X1 + $this->GAreaXOffset;
       foreach ( $Data as $Key => $Values )
        {
         $Value = $Data[$Key][$ColName];
         $YPos = $this->GArea_Y2 - (($Value-$this->VMin) * $this->DivisionRatio);

         $R = $this->Palette[$ColorID]["R"];
         $G = $this->Palette[$ColorID]["G"];
         $B = $this->Palette[$ColorID]["B"];
         $this->drawFilledCircle($XPos+1,$YPos+1,$BigRadius,$R,$G,$B);

         if ( $R2 !=-1 && $G2 !=-1 && $B2 !=-1 )
          $this->drawFilledCircle($XPos+1,$YPos+1,$SmallRadius,$R2,$G2,$B2);
         else
          {
           $R = $this->Palette[$ColorID]["R"]-5; if ( $R < 0 ) { $R = 0; }
           $G = $this->Palette[$ColorID]["G"]-5; if ( $G < 0 ) { $G = 0; }
           $B = $this->Palette[$ColorID]["B"]-5; if ( $B < 0 ) { $B = 0; }

           $this->drawFilledCircle($XPos+1,$YPos+1,$SmallRadius,$R,$G,$B);
          }

         $XPos = $XPos + $this->DivisionWidth;
        }
       $GraphID++;
      }
    }


   /* This function draw an area between two series */
   function drawArea($Data,$Serie1,$Serie2,$R,$G,$B,$Alpha = 50)
    {
     $LayerWidth  = $this->GArea_X2-$this->GArea_X1;
     $LayerHeight = $this->GArea_Y2-$this->GArea_Y1;

     $this->Layers[0] = imagecreatetruecolor($LayerWidth,$LayerHeight);
     $C_White         = imagecolorallocate($this->Layers[0],255,255,255);
     imagefilledrectangle($this->Layers[0],0,0,$LayerWidth,$LayerHeight,$C_White);
     imagecolortransparent($this->Layers[0],$C_White);

     $C_Graph = imagecolorallocate($this->Layers[0],$R,$G,$B);

     $XPos     = $this->GAreaXOffset;
     $LastXPos = -1;
     foreach ( $Data as $Key => $Values )
      {
       $Value1 = $Data[$Key][$Serie1];
       $Value2 = $Data[$Key][$Serie2];
       $YPos1  = $LayerHeight - (($Value1-$this->VMin) * $this->DivisionRatio);
       $YPos2  = $LayerHeight - (($Value2-$this->VMin) * $this->DivisionRatio);

       if ( $LastXPos != -1 )
        {
         $Points   = "";
         $Points[] = $LastXPos; $Points[] = $LastYPos1;
         $Points[] = $LastXPos; $Points[] = $LastYPos2;
         $Points[] = $XPos; $Points[] = $YPos2;
         $Points[] = $XPos; $Points[] = $YPos1;

         imagefilledpolygon($this->Layers[0],$Points,4,$C_Graph);
        }

       $LastYPos1 = $YPos1;
       $LastYPos2 = $YPos2;
       $LastXPos  = $XPos;

       $XPos = $XPos + $this->DivisionWidth;
      }

     imagecopymerge($this->Picture,$this->Layers[0],$this->GArea_X1,$this->GArea_Y1,0,0,$LayerWidth,$LayerHeight,$Alpha);
     imagedestroy($this->Layers[0]);
    }


   /* This function write the values of the specified series */
   function writeValues($Data,$DataDescription,$Series)
    {
     if ( !is_array($Series) ) { $Series = array($Series); }     

     foreach($Series as $Key => $Serie)
      {
       $ID = 0;
       foreach ( $DataDescription["Description"] as $keyI => $ValueI )
        { if ( $keyI == $Serie ) { $ColorID = $ID; }; $ID++; }

       $XPos  = $this->GArea_X1 + $this->GAreaXOffset;
       $XLast = -1;
       foreach ( $Data as $Key => $Values )
        {
         if ( isset($Data[$Key][$Serie]))
          {
           $Value = $Data[$Key][$Serie];
           $YPos = $this->GArea_Y2 - (($Value-$this->VMin) * $this->DivisionRatio);

           $Positions = imagettfbbox($this->FontSize,0,$this->FontName,$Value);
           $Width  = $Positions[2] - $Positions[6]; $XOffset = $XPos - ($Width/2); 
           $Height = $Positions[3] - $Positions[7]; $YOffset = $YPos - 4;

           $C_TextColor = imagecolorallocate($this->Picture,$this->Palette[$ColorID]["R"],$this->Palette[$ColorID]["G"],$this->Palette[$ColorID]["B"]);
           imagettftext($this->Picture,$this->FontSize,0,$XOffset,$YOffset,$C_TextColor,$this->FontName,$Value);
          }
         $XPos = $XPos + $this->DivisionWidth;
        }

      }
    }


   /* This function draw a line graph */
   function drawLineGraph($Data,$DataDescription)
    {
     $GraphID = 0;
     foreach ( $DataDescription["Values"] as $Key2 => $ColName )
      {
       $ID = 0;
       foreach ( $DataDescription["Description"] as $keyI => $ValueI )
        { if ( $keyI == $ColName ) { $ColorID = $ID; }; $ID++; }

       $XPos  = $this->GArea_X1 + $this->GAreaXOffset;
       $XLast = -1;
       foreach ( $Data as $Key => $Values )
        {
         if ( isset($Data[$Key][$ColName]))
          {
           $Value = $Data[$Key][$ColName];
           $YPos = $this->GArea_Y2 - (($Value-$this->VMin) * $this->DivisionRatio);

           if ( $XLast != -1 )
            $this->drawLine($XLast,$YLast,$XPos,$YPos,$this->Palette[$ColorID]["R"],$this->Palette[$ColorID]["G"],$this->Palette[$ColorID]["B"],TRUE);

           $XLast = $XPos;
           $YLast = $YPos;
          }
         $XPos = $XPos + $this->DivisionWidth;
        }
       $GraphID++;
      }
    }

   /* This function draw a cubic curve */
   function drawCubicCurve($Data,$DataDescription,$Accuracy=.1)
    {
     $GraphID = 0;
     foreach ( $DataDescription["Values"] as $Key2 => $ColName )
      {
       $XIn = ""; $Yin = ""; $Yt = ""; $U = "";
       $XIn[0] = 0; $YIn[0] = 0;

       $ID = 0;
       foreach ( $DataDescription["Description"] as $keyI => $ValueI )
        { if ( $keyI == $ColName ) { $ColorID = $ID; }; $ID++; }

       $Index = 1;
       $XLast = -1;
       foreach ( $Data as $Key => $Values )
        {
         if ( isset($Data[$Key][$ColName]) )
          {
           $Value = $Data[$Key][$ColName];
           $XIn[$Index] = $Index;
           $YIn[$Index] = $Value;
           $Index++;
          }
        }
       $Index--;
 
       $Yt[0] = 0;
       $Yt[1] = 0;
       $U[1]  = 0;
       for($i=2;$i<=$Index-1;$i++)
        {
         $Sig    = ($XIn[$i] - $XIn[$i-1]) / ($XIn[$i+1] - $XIn[$i-1]);
         $p      = $Sig * $Yt[$i-1] + 2;
         $Yt[$i] = ($Sig - 1) / $p;
         $U[$i]  = ($YIn[$i+1] - $YIn[$i]) / ($XIn[$i+1] - $XIn[$i]) - ($YIn[$i] - $YIn[$i-1]) / ($XIn[$i] - $XIn[$i-1]);
         $U[$i]  = (6 * $U[$i] / ($XIn[$i+1] - $XIn[$i-1]) - $Sig * $U[$i-1]) / $p;
        }

       $qn = 0;
       $un = 0;
       $Yt[$Index] = ($un - $qn * $U[$Index-1]) / ($qn * $Yt[$Index-1] + 1);

       for($k=$Index-1;$k>=1;$k--)
        $Yt[$k] = $Yt[$k] * $Yt[$k+1] + $U[$k];

       $XPos  = $this->GArea_X1 + $this->GAreaXOffset;
       for($X=1;$X<=$Index;$X=$X+$Accuracy)
        {
         $klo = 1;
         $khi = $Index;
         $k   = $khi - $klo;
         while($k > 1)
          {
           $k = $khi - $klo;
           If ( $XIn[$k] >= $X )
            $khi = $k;
           else
            $klo = $k;
          }
         $klo = $khi - 1;

         $h     = $XIn[$khi] - $XIn[$klo];
         $a     = ($XIn[$khi] - $X) / $h;
         $b     = ($X - $XIn[$klo]) / $h;
         $Value = $a * $YIn[$klo] + $b * $YIn[$khi] + (($a*$a*$a - $a) * $Yt[$klo] + ($b*$b*$b - $b) * $Yt[$khi]) * ($h*$h) / 6;

         $YPos = $this->GArea_Y2 - (($Value-$this->VMin) * $this->DivisionRatio);

         if ( $XLast != -1 )
          $this->drawLine($XLast,$YLast,$XPos,$YPos,$this->Palette[$ColorID]["R"],$this->Palette[$ColorID]["G"],$this->Palette[$ColorID]["B"],TRUE);

         $XLast = $XPos;
         $YLast = $YPos;
         $XPos  = $XPos + $this->DivisionWidth * $Accuracy;
        }

       // Add potentialy missing values
       $XPos  = $XPos - $this->DivisionWidth * $Accuracy;
       if ( $XPos < ($this->GArea_X2 - $this->GAreaXOffset) )
        {
         $YPos = $this->GArea_Y2 - (($YIn[$Index]-$this->VMin) * $this->DivisionRatio);
         $this->drawLine($XLast,$YLast,$this->GArea_X2-$this->GAreaXOffset,$YPos,$this->Palette[$ColorID]["R"],$this->Palette[$ColorID]["G"],$this->Palette[$ColorID]["B"],TRUE);
        }

       $GraphID++;
      }
    }

   /* This function draw a filled cubic curve */
   function drawFilledCubicCurve($Data,$DataDescription,$Accuracy=.1,$Alpha=100,$AroundZero=FALSE)
    {
     $LayerWidth  = $this->GArea_X2-$this->GArea_X1;
     $LayerHeight = $this->GArea_Y2-$this->GArea_Y1;
     $YZero = $LayerHeight - ((0-$this->VMin) * $this->DivisionRatio);
     if ( $YZero > $LayerHeight ) { $YZero = $LayerHeight; }

     $GraphID = 0;
     foreach ( $DataDescription["Values"] as $Key2 => $ColName )
      {
       $XIn = ""; $Yin = ""; $Yt = ""; $U = "";
       $XIn[0] = 0; $YIn[0] = 0;

       $ID = 0;
       foreach ( $DataDescription["Description"] as $keyI => $ValueI )
        { if ( $keyI == $ColName ) { $ColorID = $ID; }; $ID++; }

       $Index = 1;
       $XLast = -1;
       foreach ( $Data as $Key => $Values )
        {
         $Value = $Data[$Key][$ColName];
         $XIn[$Index] = $Index;
         $YIn[$Index] = $Value;
         $Index++;
        }
       $Index--;
 
       $Yt[0] = 0;
       $Yt[1] = 0;
       $U[1]  = 0;
       for($i=2;$i<=$Index-1;$i++)
        {
         $Sig    = ($XIn[$i] - $XIn[$i-1]) / ($XIn[$i+1] - $XIn[$i-1]);
         $p      = $Sig * $Yt[$i-1] + 2;
         $Yt[$i] = ($Sig - 1) / $p;
         $U[$i]  = ($YIn[$i+1] - $YIn[$i]) / ($XIn[$i+1] - $XIn[$i]) - ($YIn[$i] - $YIn[$i-1]) / ($XIn[$i] - $XIn[$i-1]);
         $U[$i]  = (6 * $U[$i] / ($XIn[$i+1] - $XIn[$i-1]) - $Sig * $U[$i-1]) / $p;
        }

       $qn = 0;
       $un = 0;
       $Yt[$Index] = ($un - $qn * $U[$Index-1]) / ($qn * $Yt[$Index-1] + 1);

       for($k=$Index-1;$k>=1;$k--)
        $Yt[$k] = $Yt[$k] * $Yt[$k+1] + $U[$k];

       $Points   = "";
       $Points[] = $this->GAreaXOffset;
       $Points[] = $LayerHeight;

       $this->Layers[0] = imagecreatetruecolor($LayerWidth,$LayerHeight);
       $C_White         = imagecolorallocate($this->Layers[0],255,255,255);
       imagefilledrectangle($this->Layers[0],0,0,$LayerWidth,$LayerHeight,$C_White);
       imagecolortransparent($this->Layers[0],$C_White);

       $YLast = NULL;
       $XPos  = $this->GAreaXOffset; $PointsCount = 2;
       for($X=1;$X<=$Index;$X=$X+$Accuracy)
        {
         $klo = 1;
         $khi = $Index;
         $k   = $khi - $klo;
         while($k > 1)
          {
           $k = $khi - $klo;
           If ( $XIn[$k] >= $X )
            $khi = $k;
           else
            $klo = $k;
          }
         $klo = $khi - 1;

         $h     = $XIn[$khi] - $XIn[$klo];
         $a     = ($XIn[$khi] - $X) / $h;
         $b     = ($X - $XIn[$klo]) / $h;
         $Value = $a * $YIn[$klo] + $b * $YIn[$khi] + (($a*$a*$a - $a) * $Yt[$klo] + ($b*$b*$b - $b) * $Yt[$khi]) * ($h*$h) / 6;

         $YPos = $LayerHeight - (($Value-$this->VMin) * $this->DivisionRatio);

         if ( $YLast != NULL && $AroundZero )
          {
           $aPoints   = "";
           $aPoints[] = $XLast;
           $aPoints[] = $YLast;
           $aPoints[] = $XPos;
           $aPoints[] = $YPos;
           $aPoints[] = $XPos;
           $aPoints[] = $YZero;
           $aPoints[] = $XLast;
           $aPoints[] = $YZero;

           $C_Graph = imagecolorallocate($this->Layers[0],$this->Palette[$ColorID]["R"],$this->Palette[$ColorID]["G"],$this->Palette[$ColorID]["B"]);
           imagefilledpolygon($this->Layers[0],$aPoints,4,$C_Graph);
          }
         $YLast = $YPos; $XLast = $XPos; 

         $PointsCount++;
         $Points[] = $XPos;
         $Points[] = $YPos;

         $XPos  = $XPos + $this->DivisionWidth * $Accuracy;
        }

       // Add potentialy missing values
       $XPos  = $XPos - $this->DivisionWidth * $Accuracy;
       if ( $XPos < ($LayerWidth-$this->GAreaXOffset) )
        {
         $YPos = $LayerHeight - (($YIn[$Index]-$this->VMin) * $this->DivisionRatio);

         if ( $YLast != NULL && $AroundZero )
          {
           $aPoints   = "";
           $aPoints[] = $XLast;
           $aPoints[] = $YLast;
           $aPoints[] = $LayerWidth-$this->GAreaXOffset;
           $aPoints[] = $YPos;
           $aPoints[] = $LayerWidth-$this->GAreaXOffset;
           $aPoints[] = $YZero;
           $aPoints[] = $XLast;
           $aPoints[] = $YZero;

           $C_Graph = imagecolorallocate($this->Layers[0],$this->Palette[$ColorID]["R"],$this->Palette[$ColorID]["G"],$this->Palette[$ColorID]["B"]);
           imagefilledpolygon($this->Layers[0],$aPoints,4,$C_Graph);
          }

         $PointsCount++;
         $Points[] = $LayerWidth-$this->GAreaXOffset;
         $Points[] = $YPos;
        }

       $Points[] = $LayerWidth-$this->GAreaXOffset;
       $Points[] = $LayerHeight;

       if ( !$AroundZero )
        {
         $C_Graph = imagecolorallocate($this->Layers[0],$this->Palette[$ColorID]["R"],$this->Palette[$ColorID]["G"],$this->Palette[$ColorID]["B"]);
         imagefilledpolygon($this->Layers[0],$Points,$PointsCount,$C_Graph);
        }

       imagecopymerge($this->Picture,$this->Layers[0],$this->GArea_X1,$this->GArea_Y1,0,0,$LayerWidth,$LayerHeight,$Alpha);
       imagedestroy($this->Layers[0]);

       for($i=2;$i<=($PointsCount*2-6);$i=$i+2)
        $this->drawLine($Points[$i]+$this->GArea_X1,$Points[$i+1]+$this->GArea_Y1,$Points[$i+2]+$this->GArea_X1,$Points[$i+3]+$this->GArea_Y1,$this->Palette[$ColorID]["R"],$this->Palette[$ColorID]["G"],$this->Palette[$ColorID]["B"],TRUE);

       $GraphID++;
      }
    }

   /* This function draw a filled line graph */
   function drawFilledLineGraph($Data,$DataDescription,$Alpha=100,$AroundZero=FALSE)
    {
     $LayerWidth  = $this->GArea_X2-$this->GArea_X1;
     $LayerHeight = $this->GArea_Y2-$this->GArea_Y1;

     $GraphID = 0;
     foreach ( $DataDescription["Values"] as $Key2 => $ColName )
      {
       $ID = 0;
       foreach ( $DataDescription["Description"] as $keyI => $ValueI )
        { if ( $keyI == $ColName ) { $ColorID = $ID; }; $ID++; }

       $aPoints   = "";
       $aPoints[] = $this->GAreaXOffset;
       $aPoints[] = $LayerHeight;

       $this->Layers[0] = imagecreatetruecolor($LayerWidth,$LayerHeight);
       $C_White         = imagecolorallocate($this->Layers[0],255,255,255);
       imagefilledrectangle($this->Layers[0],0,0,$LayerWidth,$LayerHeight,$C_White);
       imagecolortransparent($this->Layers[0],$C_White);

       $XPos  = $this->GAreaXOffset;
       $XLast = -1; $PointsCount = 2;
       $YZero = $LayerHeight - ((0-$this->VMin) * $this->DivisionRatio);
       if ( $YZero > $LayerHeight ) { $YZero = $LayerHeight; }

       $YLast = NULL;
       foreach ( $Data as $Key => $Values )
        {
         $Value = $Data[$Key][$ColName];
         $YPos = $LayerHeight - (($Value-$this->VMin) * $this->DivisionRatio);

         $PointsCount++;
         $aPoints[] = $XPos;
         $aPoints[] = $YPos;

         if ($YLast <> NULL && $AroundZero == TRUE)
          {
           $Points   = "";
           $Points[] = $XLast;
           $Points[] = $YLast;
           $Points[] = $XPos;
           $Points[] = $YPos;
           $Points[] = $XPos;
           $Points[] = $YZero;
           $Points[] = $XLast;
           $Points[] = $YZero;

           $C_Graph = imagecolorallocate($this->Layers[0],$this->Palette[$ColorID]["R"],$this->Palette[$ColorID]["G"],$this->Palette[$ColorID]["B"]);
           imagefilledpolygon($this->Layers[0],$Points,4,$C_Graph);
          }

         $YLast = $YPos; $XLast = $XPos;
         $XPos  = $XPos + $this->DivisionWidth;
        }
       $aPoints[] = $LayerWidth - $this->GAreaXOffset;
       $aPoints[] = $LayerHeight;

       if ( $AroundZero == FALSE )
        {
         $C_Graph = imagecolorallocate($this->Layers[0],$this->Palette[$ColorID]["R"],$this->Palette[$ColorID]["G"],$this->Palette[$ColorID]["B"]);
         imagefilledpolygon($this->Layers[0],$aPoints,$PointsCount,$C_Graph);
        }

       imagecopymerge($this->Picture,$this->Layers[0],$this->GArea_X1,$this->GArea_Y1,0,0,$LayerWidth,$LayerHeight,$Alpha);
       imagedestroy($this->Layers[0]);

       for($i=2;$i<=($PointsCount*2-6);$i=$i+2)
        $this->drawLine($aPoints[$i]+$this->GArea_X1,$aPoints[$i+1]+$this->GArea_Y1,$aPoints[$i+2]+$this->GArea_X1,$aPoints[$i+3]+$this->GArea_Y1,$this->Palette[$ColorID]["R"],$this->Palette[$ColorID]["G"],$this->Palette[$ColorID]["B"],TRUE);

       $GraphID++;
      }
    }

   /* This function draw a bar graph */
   function drawOverlayBarGraph($Data,$DataDescription,$Alpha=50)
    {
     $LayerWidth  = $this->GArea_X2-$this->GArea_X1;
     $LayerHeight = $this->GArea_Y2-$this->GArea_Y1;

     $GraphID = 0;
     foreach ( $DataDescription["Values"] as $Key2 => $ColName )
      {
       $ID = 0;
       foreach ( $DataDescription["Description"] as $keyI => $ValueI )
        { if ( $keyI == $ColName ) { $ColorID = $ID; }; $ID++; }

       $this->Layers[$GraphID] = imagecreatetruecolor($LayerWidth,$LayerHeight);
       $C_White                = imagecolorallocate($this->Layers[$GraphID],255,255,255);
       $C_Graph                = imagecolorallocate($this->Layers[$GraphID],$this->Palette[$GraphID]["R"],$this->Palette[$GraphID]["G"],$this->Palette[$GraphID]["B"]);
       imagefilledrectangle($this->Layers[$GraphID],0,0,$LayerWidth,$LayerHeight,$C_White);
       imagecolortransparent($this->Layers[$GraphID],$C_White);

       $XWidth = $this->DivisionWidth / 4;
       $XPos   = $this->GAreaXOffset;
       $YZero  = $LayerHeight - ((0-$this->VMin) * $this->DivisionRatio);
       $XLast  = -1; $PointsCount = 2;
       foreach ( $Data as $Key => $Values )
        {
         if ( isset($Data[$Key][$ColName]) )
          {
           $Value = $Data[$Key][$ColName];
           $YPos  = $LayerHeight - (($Value-$this->VMin) * $this->DivisionRatio);

           imagefilledrectangle($this->Layers[$GraphID],$XPos-$XWidth,$YPos,$XPos+$XWidth,$YZero,$C_Graph);

           $X1 = floor($XPos - $XWidth + $this->GArea_X1); $Y1 = floor($YPos+$this->GArea_Y1) + .2;
           $X2 = floor($XPos + $XWidth + $this->GArea_X1);
           if ( $X1 <= $this->GArea_X1 ) { $X1 = $this->GArea_X1 + 1; }
           if ( $X2 >= $this->GArea_X2 ) { $X2 = $this->GArea_X2 - 1; }

           $this->drawLine($X1,$Y1,$X2,$Y1,$this->Palette[$ColorID]["R"],$this->Palette[$ColorID]["G"],$this->Palette[$ColorID]["B"],TRUE);
          }
         $XPos = $XPos + $this->DivisionWidth;
        }

       $GraphID++;
      }

     for($i=0;$i<=($GraphID-1);$i++)
      {
       imagecopymerge($this->Picture,$this->Layers[$i],$this->GArea_X1,$this->GArea_Y1,0,0,$LayerWidth,$LayerHeight,$Alpha);
       imagedestroy($this->Layers[$i]);
      }
    }

   /* This function draw a bar graph */
   function drawBarGraph($Data,$DataDescription,$Shadow=FALSE)
    {
     $GraphID      = 0;
     $Series       = count($DataDescription["Values"]);
     $SeriesWidth  = $this->DivisionWidth / ($Series+1);
     $SerieXOffset = $this->DivisionWidth / 2 - $SeriesWidth / 2;

     $YZero  = $this->GArea_Y2 - ((0-$this->VMin) * $this->DivisionRatio);
     if ( $YZero > $this->GArea_Y2 ) { $YZero = $this->GArea_Y2; }

     $SerieID = 0;
     foreach ( $DataDescription["Values"] as $Key2 => $ColName )
      {
       $ID = 0;
       foreach ( $DataDescription["Description"] as $keyI => $ValueI )
        { if ( $keyI == $ColName ) { $ColorID = $ID; }; $ID++; }

       $XPos  = $this->GArea_X1 + $this->GAreaXOffset - $SerieXOffset + $SeriesWidth * $SerieID;
       $XLast = -1;
       foreach ( $Data as $Key => $Values )
        {
         if ( isset($Data[$Key][$ColName]))
          {
           $Value = $Data[$Key][$ColName];
           $YPos = $this->GArea_Y2 - (($Value-$this->VMin) * $this->DivisionRatio);
           
           if ( $Shadow )
            $this->drawRectangle($XPos+1,$YZero,$XPos+$SeriesWidth-1,$YPos,25,25,25);
           $this->drawFilledRectangle($XPos+1,$YZero,$XPos+$SeriesWidth-1,$YPos,$this->Palette[$ColorID]["R"],$this->Palette[$ColorID]["G"],$this->Palette[$ColorID]["B"]);
          }
         $XPos = $XPos + $this->DivisionWidth;
        }
       $SerieID++;
      }
    }

   /* This function draw a limits bar graphs */
   function drawLimitsGraph($Data,$DataDescription,$R=0,$G=0,$B=0)
    {
     $XWidth = $this->DivisionWidth / 4;
     $XPos   = $this->GArea_X1 + $this->GAreaXOffset;

     foreach ( $Data as $Key => $Values )
      {
       $Min     = $Data[$Key][$DataDescription["Values"][0]];
       $Max     = $Data[$Key][$DataDescription["Values"][0]];
       $GraphID = 0; $MaxID = 0; $MinID = 0;
       foreach ( $DataDescription["Values"] as $Key2 => $ColName )
        {
         if ( isset($Data[$Key][$ColName]) )
          {
           if ( $Data[$Key][$ColName] > $Max )
            { $Max = $Data[$Key][$ColName]; $MaxID = $GraphID; }
          }
         if ( isset($Data[$Key][$ColName]) )
          {
           if ( $Data[$Key][$ColName] < $Min )
            { $Min = $Data[$Key][$ColName]; $MinID = $GraphID; }
           $GraphID++;
          }
        }

       $YPos = $this->GArea_Y2 - (($Max-$this->VMin) * $this->DivisionRatio);
       $X1 = floor($XPos - $XWidth); $Y1 = floor($YPos) - .2;
       $X2 = floor($XPos + $XWidth);
       if ( $X1 <= $this->GArea_X1 ) { $X1 = $this->GArea_X1 + 1; }
       if ( $X2 >= $this->GArea_X2 ) { $X2 = $this->GArea_X2 - 1; }

       $YPos = $this->GArea_Y2 - (($Min-$this->VMin) * $this->DivisionRatio);
       $Y2 = floor($YPos) + .2;

       $this->drawLine(floor($XPos)-.2,$Y1+1,floor($XPos)-.2,$Y2-1,$R,$G,$B,TRUE);
       $this->drawLine(floor($XPos)+.2,$Y1+1,floor($XPos)+.2,$Y2-1,$R,$G,$B,TRUE);
       $this->drawLine($X1,$Y1,$X2,$Y1,$this->Palette[$MaxID]["R"],$this->Palette[$MaxID]["G"],$this->Palette[$MaxID]["B"],TRUE);
       $this->drawLine($X1,$Y2,$X2,$Y2,$this->Palette[$MinID]["R"],$this->Palette[$MinID]["G"],$this->Palette[$MinID]["B"],TRUE);

       $XPos = $XPos + $this->DivisionWidth;
      }
    }

   /* This function draw radar axis centered on the graph area */
   function drawRadarAxis($Data,$DataDescription,$Mosaic=TRUE,$BorderOffset=10,$A_R=60,$A_G=60,$A_B=60,$S_R=200,$S_G=200,$S_B=200,$MaxValue=-1)
    {
     $C_TextColor = imagecolorallocate($this->Picture,$A_R,$A_G,$A_B);

     /* Draw radar axis */
     $Points  = count($Data);
     $Radius  = ( $this->GArea_Y2 - $this->GArea_Y1 ) / 2 - $BorderOffset;
     $XCenter = ( $this->GArea_X2 - $this->GArea_X1 ) / 2 + $this->GArea_X1;
     $YCenter = ( $this->GArea_Y2 - $this->GArea_Y1 ) / 2 + $this->GArea_Y1;

     /* Search for the max value */
     if ( $MaxValue == -1 )
      {
       foreach ( $DataDescription["Values"] as $Key2 => $ColName )
        {
         foreach ( $Data as $Key => $Values )
          {
           if ( isset($Data[$Key][$ColName]))
            if ( $Data[$Key][$ColName] > $MaxValue ) { $MaxValue = $Data[$Key][$ColName]; }
          }
        }
      }

     /* Draw the mosaic */
     if ( $Mosaic )
      {
       $RadiusScale = $Radius / $MaxValue;
       for ( $t=1; $t<=$MaxValue-1; $t++)
        {
         $TRadius  = $RadiusScale * $t;
         $LastX1   = -1;

         for ( $i=0; $i<=$Points; $i++)
          {
           $Angle = -90 + $i * 360/$Points;
           $X1 = cos($Angle * 3.1418 / 180 ) * $TRadius + $XCenter;
           $Y1 = sin($Angle * 3.1418 / 180 ) * $TRadius + $YCenter;
           $X2 = cos($Angle * 3.1418 / 180 ) * ($TRadius+$RadiusScale) + $XCenter;
           $Y2 = sin($Angle * 3.1418 / 180 ) * ($TRadius+$RadiusScale) + $YCenter;

           if ( $t % 2 == 1 && $LastX1 != -1)
            {
             $Plots   = "";
             $Plots[] = $X1; $Plots[] = $Y1;
             $Plots[] = $X2; $Plots[] = $Y2;
             $Plots[] = $LastX2; $Plots[] = $LastY2;
             $Plots[] = $LastX1; $Plots[] = $LastY1;

             $C_Graph = imagecolorallocate($this->Picture,250,250,250);
             imagefilledpolygon($this->Picture,$Plots,(count($Plots)+1)/2,$C_Graph);
            }

           $LastX1 = $X1; $LastY1= $Y1;
           $LastX2 = $X2; $LastY2= $Y2;
          }
        }
      }


     /* Draw the spider web */
     for ( $t=1; $t<=$MaxValue; $t++)
      {
       $TRadius = ( $Radius / $MaxValue ) * $t;
       $LastX   = -1;

       for ( $i=0; $i<=$Points; $i++)
        {
         $Angle = -90 + $i * 360/$Points;
         $X = cos($Angle * 3.1418 / 180 ) * $TRadius + $XCenter;
         $Y = sin($Angle * 3.1418 / 180 ) * $TRadius + $YCenter;

         if ( $LastX != -1 )
          $this->drawDottedLine($LastX,$LastY,$X,$Y,4,$S_R,$S_G,$S_B);

         $LastX = $X; $LastY= $Y;
        }
      }

     /* Draw the axis */
     for ( $i=0; $i<=$Points; $i++)
      {
       $Angle = -90 + $i * 360/$Points;
       $X = cos($Angle * 3.1418 / 180 ) * $Radius + $XCenter;
       $Y = sin($Angle * 3.1418 / 180 ) * $Radius + $YCenter;

       $this->drawLine($XCenter,$YCenter,$X,$Y,$A_R,$A_G,$A_B);

       $XOffset = 0; $YOffset = 0;
       if (isset($Data[$i][$DataDescription["Position"]]))
        {
         $Label = $Data[$i][$DataDescription["Position"]];

         $Positions = imagettfbbox($this->FontSize,0,$this->FontName,$Label);
         $Width  = $Positions[2] - $Positions[6];
         $Height = $Positions[3] - $Positions[7];

         if ( $Angle >= 0 && $Angle <= 90 )
          $YOffset = $Height;

         if ( $Angle > 90 && $Angle <= 180 )
          { $YOffset = $Height; $XOffset = -$Width; }

         if ( $Angle > 180 && $Angle <= 270 )
          { $XOffset = -$Width; }

         imagettftext($this->Picture,$this->FontSize,0,$X+$XOffset,$Y+$YOffset,$C_TextColor,$this->FontName,$Label);
        }
      }

     /* Write the values */
     for ( $t=1; $t<=$MaxValue; $t++)
      {
       $TRadius = ( $Radius / $MaxValue ) * $t;

       $Angle = -90 + 360 / $Points;
       $X1 = $XCenter;
       $Y1 = $YCenter - $TRadius;
       $X2 = cos($Angle * 3.1418 / 180 ) * $TRadius + $XCenter;
       $Y2 = sin($Angle * 3.1418 / 180 ) * $TRadius + $YCenter;

       $XPos = floor(($X2-$X1)/2) + $X1;
       $YPos = floor(($Y2-$Y1)/2) + $Y1;

       $Positions = imagettfbbox($this->FontSize,0,$this->FontName,$t);
       $X = $XPos - ( $X+$Positions[2] - $X+$Positions[6] ) / 2;
       $Y = $YPos + $this->FontSize;

       $this->drawFilledRoundedRectangle($X+$Positions[6]-2,$Y+$Positions[7]-1,$X+$Positions[2]+4,$Y+$Positions[3]+1,2,240,240,240);
       $this->drawRoundedRectangle($X+$Positions[6]-2,$Y+$Positions[7]-1,$X+$Positions[2]+4,$Y+$Positions[3]+1,2,220,220,220);
       imagettftext($this->Picture,$this->FontSize,0,$X,$Y,$C_TextColor,$this->FontName,$t);
      }
    }

   /* This function draw a radar graph centered on the graph area */
   function drawRadar($Data,$DataDescription,$BorderOffset=10,$MaxValue=-1)
    {
     $Points  = count($Data);
     $Radius  = ( $this->GArea_Y2 - $this->GArea_Y1 ) / 2 - $BorderOffset;
     $XCenter = ( $this->GArea_X2 - $this->GArea_X1 ) / 2 + $this->GArea_X1;
     $YCenter = ( $this->GArea_Y2 - $this->GArea_Y1 ) / 2 + $this->GArea_Y1;

     /* Search for the max value */
     if ( $MaxValue == -1 )
      {
       foreach ( $DataDescription["Values"] as $Key2 => $ColName )
        {
         foreach ( $Data as $Key => $Values )
          {
           if ( isset($Data[$Key][$ColName]))
            if ( $Data[$Key][$ColName] > $MaxValue ) { $MaxValue = $Data[$Key][$ColName]; }
          }
        }
      }

     $GraphID = 0;
     foreach ( $DataDescription["Values"] as $Key2 => $ColName )
      {
       $ID = 0;
       foreach ( $DataDescription["Description"] as $keyI => $ValueI )
        { if ( $keyI == $ColName ) { $ColorID = $ID; }; $ID++; }

       $Angle = -90;
       $XLast = -1;
       foreach ( $Data as $Key => $Values )
        {
         if ( isset($Data[$Key][$ColName]))
          {
           $Value    = $Data[$Key][$ColName];
           $Strength = ( $Radius / $MaxValue ) * $Value;

           $XPos = cos($Angle * 3.1418 / 180 ) * $Strength + $XCenter;
           $YPos = sin($Angle * 3.1418 / 180 ) * $Strength + $YCenter;

           if ( $XLast != -1 )
            $this->drawLine($XLast,$YLast,$XPos,$YPos,$this->Palette[$ColorID]["R"],$this->Palette[$ColorID]["G"],$this->Palette[$ColorID]["B"]);

           if ( $XLast == -1 )
            { $FirstX = $XPos; $FirstY = $YPos; }

           $Angle = $Angle + (360/$Points);
           $XLast = $XPos;
           $YLast = $YPos;
          }
        }
       $this->drawLine($XPos,$YPos,$FirstX,$FirstY,$this->Palette[$ColorID]["R"],$this->Palette[$ColorID]["G"],$this->Palette[$ColorID]["B"]);
       $GraphID++;
      }
    }

   /* This function draw a radar graph centered on the graph area */
   function drawFilledRadar($Data,$DataDescription,$Alpha=50,$BorderOffset=10,$MaxValue=-1)
    {
     $Points      = count($Data);
     $LayerWidth  = $this->GArea_X2-$this->GArea_X1;
     $LayerHeight = $this->GArea_Y2-$this->GArea_Y1;
     $Radius      = ( $this->GArea_Y2 - $this->GArea_Y1 ) / 2 - $BorderOffset;
     $XCenter     = ( $this->GArea_X2 - $this->GArea_X1 ) / 2;
     $YCenter     = ( $this->GArea_Y2 - $this->GArea_Y1 ) / 2;

     /* Search for the max value */
     if ( $MaxValue == -1 )
      {
       foreach ( $DataDescription["Values"] as $Key2 => $ColName )
        {
         foreach ( $Data as $Key => $Values )
          {
           if ( isset($Data[$Key][$ColName]))
            if ( $Data[$Key][$ColName] > $MaxValue ) { $MaxValue = $Data[$Key][$ColName]; }
          }
        }
      }

     $GraphID = 0;
     foreach ( $DataDescription["Values"] as $Key2 => $ColName )
      {
       $ID = 0;
       foreach ( $DataDescription["Description"] as $keyI => $ValueI )
        { if ( $keyI == $ColName ) { $ColorID = $ID; }; $ID++; }

       $Angle = -90;
       $XLast = -1;
       $Plots = "";
       foreach ( $Data as $Key => $Values )
        {
         if ( isset($Data[$Key][$ColName]))
          {
           $Value    = $Data[$Key][$ColName];
           $Strength = ( $Radius / $MaxValue ) * $Value;

           $XPos = cos($Angle * 3.1418 / 180 ) * $Strength + $XCenter;
           $YPos = sin($Angle * 3.1418 / 180 ) * $Strength + $YCenter;

           $Plots[] = $XPos;
           $Plots[] = $YPos;

           $Angle = $Angle + (360/$Points);
           $XLast = $XPos;
           $YLast = $YPos;
          }
        }

       if (isset($Plots[0]))
        {
         $Plots[] = $Plots[0];
         $Plots[] = $Plots[1];

         $this->Layers[0] = imagecreatetruecolor($LayerWidth,$LayerHeight);
         $C_White         = imagecolorallocate($this->Layers[0],255,255,255);
         imagefilledrectangle($this->Layers[0],0,0,$LayerWidth,$LayerHeight,$C_White);
         imagecolortransparent($this->Layers[0],$C_White);

         $C_Graph = imagecolorallocate($this->Layers[0],$this->Palette[$ColorID]["R"],$this->Palette[$ColorID]["G"],$this->Palette[$ColorID]["B"]);
         imagefilledpolygon($this->Layers[0],$Plots,(count($Plots)+1)/2,$C_Graph);

         imagecopymerge($this->Picture,$this->Layers[0],$this->GArea_X1,$this->GArea_Y1,0,0,$LayerWidth,$LayerHeight,$Alpha);
         imagedestroy($this->Layers[0]);

         for($i=0;$i<=count($Plots)-4;$i=$i+2)
          $this->drawLine($Plots[$i]+$this->GArea_X1,$Plots[$i+1]+$this->GArea_Y1,$Plots[$i+2]+$this->GArea_X1,$Plots[$i+3]+$this->GArea_Y1,$this->Palette[$ColorID]["R"],$this->Palette[$ColorID]["G"],$this->Palette[$ColorID]["B"]);
        }

       $GraphID++;
      }
    }

   /* This function draw a flat pie chart */
   function drawBasicPieGraph($Data,$DataDescription,$XPos,$YPos,$Radius=100,$DrawLabels=TRUE,$R=255,$G=255,$B=255,$Decimals=0)
    {
     /* Determine pie sum */
     $Series = 0; $PieSum = 0;
     foreach ( $DataDescription["Values"] as $Key2 => $ColName )
      {
       if ( $ColName != $DataDescription["Position"] )
        {
         $Series++;
         foreach ( $Data as $Key => $Values )
          {
           if ( isset($Data[$Key][$ColName]))
            $PieSum = $PieSum + $Data[$Key][$ColName]; $iValues[] = $Data[$Key][$ColName];
          }
        }
      }

     /* Validate serie */
     if ( $Series != 1 )
      RaiseFatal("Pie chart can only accept one serie of data.");

     $SpliceRatio         = 360 / $PieSum;
     $SplicePercent       = 100 / $PieSum;

     /* Calculate all polygons */
     $Angle    = 0; $TopPlots = "";
     foreach($iValues as $Key => $Value)
      {
       $TopPlots[$Key][] = $XPos;
       $TopPlots[$Key][] = $YPos;

       /* Process labels position & size */
       if ( $DrawLabels )
        {
         $TAngle   = $Angle+($Value*$SpliceRatio/2);
         $Caption  = (floor($Value * pow(10,$Decimals) * $SplicePercent)/pow(10,$Decimals))."%";
         $TX       = cos(($TAngle) * 3.1418 / 180 ) * ($Radius + 10)+ $XPos;
         $TY       = sin(($TAngle) * 3.1418 / 180 ) * ($Radius+ 10) + $YPos + 4;

         if ( $TAngle > 90 && $TAngle < 270 )
          {
           $Position  = imageftbbox($this->FontSize,0,$this->FontName,$Caption);
           $TextWidth = $Position[2]-$Position[0];
           $TX = $TX - $TextWidth;
          }

         $C_TextColor = imagecolorallocate($this->Picture,70,70,70);
         imagettftext($this->Picture,$this->FontSize,0,$TX,$TY,$C_TextColor,$this->FontName,$Caption);
        }

       /* Process pie slices */
       for($iAngle=$Angle;$iAngle<=$Angle+$Value*$SpliceRatio;$iAngle=$iAngle+.5)
        {
         $TopX = cos($iAngle * 3.1418 / 180 ) * $Radius + $XPos;
         $TopY = sin($iAngle * 3.1418 / 180 ) * $Radius + $YPos;

         $TopPlots[$Key][] = $TopX; 
         $TopPlots[$Key][] = $TopY;
        }

       $TopPlots[$Key][] = $XPos;
       $TopPlots[$Key][] = $YPos;

       $Angle = $iAngle;
      }
     $PolyPlots = $TopPlots;

     /* Set array values type to float --- PHP Bug with imagefilledpolygon casting to integer */
     foreach ($TopPlots as $Key => $Value)
      { foreach ($TopPlots[$Key] as $Key2 => $Value2) { settype($TopPlots[$Key][$Key2],"float"); } }

     /* Draw Top polygons */
     foreach ($PolyPlots as $Key => $Value)
      { 
       $C_GraphLo = imagecolorallocate($this->Picture,$this->Palette[$Key]["R"],$this->Palette[$Key]["G"],$this->Palette[$Key]["B"]);
       imagefilledpolygon($this->Picture,$PolyPlots[$Key],(count($PolyPlots[$Key])+1)/2,$C_GraphLo);
      }

     $this->drawCircle($XPos-.5,$YPos-.5,$Radius,$R,$G,$B);
     $this->drawCircle($XPos-.5,$YPos-.5,$Radius+.5,$R,$G,$B);

     /* Draw Top polygons */
     foreach ($TopPlots as $Key => $Value)
      { 
       for($j=0;$j<=count($TopPlots[$Key])-4;$j=$j+2)
        $this->drawLine($TopPlots[$Key][$j],$TopPlots[$Key][$j+1],$TopPlots[$Key][$j+2],$TopPlots[$Key][$j+3],$R,$G,$B);
      }
    }

   /* This function draw a flat pie chart */
   function drawFlatPieGraph($Data,$DataDescription,$XPos,$YPos,$Radius=100,$DrawLabels=TRUE,$SpliceDistance=0,$Decimals = 0)
    {
     /* Determine pie sum */
     $Series = 0; $PieSum = 0;
     foreach ( $DataDescription["Values"] as $Key2 => $ColName )
      {
       if ( $ColName != $DataDescription["Position"] )
        {
         $Series++;
         foreach ( $Data as $Key => $Values )
          {
           if ( isset($Data[$Key][$ColName]))
            $PieSum = $PieSum + $Data[$Key][$ColName]; $iValues[] = $Data[$Key][$ColName];
          }
        }
      }

     /* Validate serie */
     if ( $Series != 1 )
      RaiseFatal("Pie chart can only accept one serie of data.");

     $SpliceDistanceRatio = $SpliceDistance;
     $SpliceRatio         = (360 - $SpliceDistanceRatio * count($iValues) ) / $PieSum;
     $SplicePercent       = 100 / $PieSum;

     /* Calculate all polygons */
     $Angle    = 0; $TopPlots = "";
     foreach($iValues as $Key => $Value)
      {
       $XCenterPos = cos(($Angle+($Value*$SpliceRatio+$SpliceDistanceRatio)/2) * 3.1418 / 180 ) * $SpliceDistance + $XPos;
       $YCenterPos = sin(($Angle+($Value*$SpliceRatio+$SpliceDistanceRatio)/2) * 3.1418 / 180 ) * $SpliceDistance + $YPos;

       $TopPlots[$Key][] = $XCenterPos;
       $TopPlots[$Key][] = $YCenterPos;

       /* Process labels position & size */
       if ( $DrawLabels )
        {
         $TAngle   = $Angle+($Value*$SpliceRatio/2);
         $Caption  = (floor($Value * pow(10,$Decimals) * $SplicePercent)/pow(10,$Decimals))."%";
         $TX       = cos(($TAngle) * 3.1418 / 180 ) * ($Radius+10+$SpliceDistance)+$XPos;
         $TY       = sin(($TAngle) * 3.1418 / 180 ) * ($Radius+10+$SpliceDistance) + $YPos + 4;

         if ( $TAngle > 90 && $TAngle < 270 )
          {
           $Position  = imageftbbox($this->FontSize,0,$this->FontName,$Caption);
           $TextWidth = $Position[2]-$Position[0];
           $TX = $TX - $TextWidth;
          }

         $C_TextColor = imagecolorallocate($this->Picture,70,70,70);
         imagettftext($this->Picture,$this->FontSize,0,$TX,$TY,$C_TextColor,$this->FontName,$Caption);
        }

       /* Draw borders to correct imagefilledpolygon bug */
       $BMax = 2;
       for($i=-1;$i<=$BMax;$i++)
        {
         $BorderX1 = cos(($Angle+($Value*$SpliceRatio+$SpliceDistanceRatio)/2) * 3.1418 / 180 ) * ($SpliceDistance+$i) + $XPos;
         $BorderY1 = sin(($Angle+($Value*$SpliceRatio+$SpliceDistanceRatio)/2) * 3.1418 / 180 ) * ($SpliceDistance+$i) + $YPos;
         $BorderX2 = cos(($Angle+$i*.5) * 3.1418 / 180 ) * (($Radius+$BMax)+$SpliceDistance) + $XPos;
         $BorderY2 = sin(($Angle+$i*.5) * 3.1418 / 180 ) * (($Radius+$BMax)+$SpliceDistance) + $YPos;
         $this->drawLine($BorderX1,$BorderY1,$BorderX2,$BorderY2,$this->Palette[$Key]["R"],$this->Palette[$Key]["G"],$this->Palette[$Key]["B"]);

         $BorderX1 = cos(($Angle+($Value*$SpliceRatio+$SpliceDistanceRatio)/2) * 3.1418 / 180 ) * ($SpliceDistance+$i) + $XPos;
         $BorderY1 = sin(($Angle+($Value*$SpliceRatio+$SpliceDistanceRatio)/2) * 3.1418 / 180 ) * ($SpliceDistance+$i) + $YPos;
         $BorderX2 = cos(($Angle-$i*.5+$Value*$SpliceRatio) * 3.1418 / 180 ) * (($Radius+$BMax)+$SpliceDistance) + $XPos;
         $BorderY2 = sin(($Angle-$i*.5+$Value*$SpliceRatio) * 3.1418 / 180 ) * (($Radius+$BMax)+$SpliceDistance) + $YPos;
         $this->drawLine($BorderX1,$BorderY1,$BorderX2,$BorderY2,$this->Palette[$Key]["R"],$this->Palette[$Key]["G"],$this->Palette[$Key]["B"]);
        }

       /* Process pie slices */
       for($iAngle=$Angle;$iAngle<=$Angle+$Value*$SpliceRatio;$iAngle=$iAngle+.5)
        {
         $TopX = cos($iAngle * 3.1418 / 180 ) * ($Radius+$SpliceDistance) + $XPos;
         $TopY = sin($iAngle * 3.1418 / 180 ) * ($Radius+$SpliceDistance) + $YPos;

         $TopPlots[$Key][] = $TopX;
         $TopPlots[$Key][] = $TopY;

         if ( $iAngle != $Angle )
          {
           for($i=-1;$i<=2;$i++)
            {
             $BorderX1 = cos(($iAngle-.5) * 3.1418 / 180 ) * (($Radius+$i)+$SpliceDistance) + $XPos;
             $BorderY1 = sin(($iAngle-.5) * 3.1418 / 180 ) * (($Radius+$i)+$SpliceDistance) + $YPos;
             $BorderX2 = cos($iAngle * 3.1418 / 180 ) * (($Radius+$i)+$SpliceDistance) + $XPos;
             $BorderY2 = sin($iAngle * 3.1418 / 180 ) * (($Radius+$i)+$SpliceDistance) + $YPos;

             $this->drawLine($BorderX1,$BorderY1,$BorderX2,$BorderY2,$this->Palette[$Key]["R"],$this->Palette[$Key]["G"],$this->Palette[$Key]["B"]);
            }
          }
        }

       $TopPlots[$Key][] = $XCenterPos;
       $TopPlots[$Key][] = $YCenterPos;

       $Angle = $iAngle + $SpliceDistanceRatio;
      }
     $PolyPlots = $TopPlots;

     /* Set array values type to float --- PHP Bug with imagefilledpolygon casting to integer */
     foreach ($TopPlots as $Key => $Value)
      { foreach ($TopPlots[$Key] as $Key2 => $Value2) { settype($TopPlots[$Key][$Key2],"float"); } }

     /* Draw Top polygons */
     foreach ($TopPlots as $Key => $Value)
      { 
       $C_GraphLo = imagecolorallocate($this->Picture,$this->Palette[$Key]["R"],$this->Palette[$Key]["G"],$this->Palette[$Key]["B"]);
       imagefilledpolygon($this->Picture,$PolyPlots[$Key],(count($PolyPlots[$Key])+1)/2,$C_GraphLo);
      }
    }

   /* This function draw a pseudo-3D pie chart */
   function drawPieGraph($Data,$DataDescription,$XPos,$YPos,$Radius=100,$DrawLabels=TRUE,$EnhanceColors=TRUE,$Skew=60,$SpliceHeight=20,$SpliceDistance=0,$Decimals = 0)
    {
     /* Determine pie sum */
     $Series = 0; $PieSum = 0;
     foreach ( $DataDescription["Values"] as $Key2 => $ColName )
      {
       if ( $ColName != $DataDescription["Position"] )
        {
         $Series++;
         foreach ( $Data as $Key => $Values )
          if ( isset($Data[$Key][$ColName]))
           {
            if ( $Data[$Key][$ColName] == 0 )
             { $PieSum = $PieSum + 1; $iValues[] = 1; }
            else
             { $PieSum = $PieSum + $Data[$Key][$ColName]; $iValues[] = $Data[$Key][$ColName]; }
           }
        }
      }

     /* Validate serie */
     if ( $Series != 1 )
      RaiseFatal("Pie chart can only accept one serie of data.");

     $SpliceDistanceRatio = $SpliceDistance;
     $SkewHeight          = ($Radius * $Skew) / 100;
     $SpliceRatio         = (360 - $SpliceDistanceRatio * count($iValues) ) / $PieSum;
     $SplicePercent       = 100 / $PieSum;

     /* Calculate all polygons */
     $Angle    = 0; $TopPlots = ""; $BotPlots = ""; $CDev = 5;
     foreach($iValues as $Key => $Value)
      {
       $XCenterPos = cos(($Angle-$CDev+($Value*$SpliceRatio+$SpliceDistanceRatio)/2) * 3.1418 / 180 ) * $SpliceDistance + $XPos;
       $YCenterPos = sin(($Angle-$CDev+($Value*$SpliceRatio+$SpliceDistanceRatio)/2) * 3.1418 / 180 ) * $SpliceDistance + $YPos;
       $XCenterPos2 = cos(($Angle+$CDev+($Value*$SpliceRatio+$SpliceDistanceRatio)/2) * 3.1418 / 180 ) * $SpliceDistance + $XPos;
       $YCenterPos2 = sin(($Angle+$CDev+($Value*$SpliceRatio+$SpliceDistanceRatio)/2) * 3.1418 / 180 ) * $SpliceDistance + $YPos;

       $TopPlots[$Key][] = $XCenterPos; $BotPlots[$Key][] = $XCenterPos;
       $TopPlots[$Key][] = $YCenterPos; $BotPlots[$Key][] = $YCenterPos + $SpliceHeight;

       /* Process labels position & size */
       if ( $DrawLabels )
        {
         $TAngle   = $Angle+($Value*$SpliceRatio/2);
         $Caption  = (floor($Value * pow(10,$Decimals) * $SplicePercent)/pow(10,$Decimals))."%";
         $TX       = cos(($TAngle) * 3.1418 / 180 ) * ($Radius + 10)+ $XPos;

         if ( $TAngle > 0 && $TAngle < 180 )
          $TY = sin(($TAngle) * 3.1418 / 180 ) * ($SkewHeight + 10) + $YPos + $SpliceHeight + 4;
         else
          $TY = sin(($TAngle) * 3.1418 / 180 ) * ($SkewHeight + 10) + $YPos + 4;

         if ( $TAngle > 90 && $TAngle < 270 )
          {
           $Position  = imageftbbox($this->FontSize,0,$this->FontName,$Caption);
           $TextWidth = $Position[2]-$Position[0];
           $TX = $TX - $TextWidth;
          }

         $C_TextColor = imagecolorallocate($this->Picture,70,70,70);
         imagettftext($this->Picture,$this->FontSize,0,$TX,$TY,$C_TextColor,$this->FontName,$Caption);
        }

       /* Process pie slices */
       for($iAngle=$Angle;$iAngle<=$Angle+$Value*$SpliceRatio;$iAngle=$iAngle+.5)
        {
         $TopX = cos($iAngle * 3.1418 / 180 ) * $Radius + $XPos;
         $TopY = sin($iAngle * 3.1418 / 180 ) * $SkewHeight + $YPos;

         $TopPlots[$Key][] = $TopX; $BotPlots[$Key][] = $TopX;
         $TopPlots[$Key][] = $TopY; $BotPlots[$Key][] = $TopY + $SpliceHeight;
        }

       $TopPlots[$Key][] = $XCenterPos2; $BotPlots[$Key][] = $XCenterPos2;
       $TopPlots[$Key][] = $YCenterPos2; $BotPlots[$Key][] = $YCenterPos2 + $SpliceHeight;

       $Angle = $iAngle + $SpliceDistanceRatio;
      }

     /* Draw Bottom polygons */
     foreach($iValues as $Key => $Value)
      {
       $C_GraphLo = AllocateColor($this->Picture,$this->Palette[$Key]["R"],$this->Palette[$Key]["G"],$this->Palette[$Key]["B"],-20);
       imagefilledpolygon($this->Picture,$BotPlots[$Key],(count($BotPlots[$Key])+1)/2,$C_GraphLo);

       for($j=0;$j<=count($BotPlots[$Key])-4;$j=$j+2)
        $this->drawLine($BotPlots[$Key][$j],$BotPlots[$Key][$j+1],$BotPlots[$Key][$j+2],$BotPlots[$Key][$j+3],$this->Palette[$Key]["R"]-20,$this->Palette[$Key]["G"]-20,$this->Palette[$Key]["B"]-20);
      }

     /* Draw pie layers */
     if ( $EnhanceColors ) { $ColorRatio = 30 / $SpliceHeight; } else { $ColorRatio = 25 / $SpliceHeight; }
     for($i=$SpliceHeight-1;$i>=1;$i--)
      {
       foreach($iValues as $Key => $Value)
        {
         $C_GraphLo = AllocateColor($this->Picture,$this->Palette[$Key]["R"],$this->Palette[$Key]["G"],$this->Palette[$Key]["B"],-10);
         $Plots = ""; $Plot = 0;
         foreach($TopPlots[$Key] as $Key2 => $Value2)
          {
           $Plot++;
           if ( $Plot % 2 == 1 )
            $Plots[] = $Value2;
           else
            $Plots[] = $Value2+$i;
          }
         imagefilledpolygon($this->Picture,$Plots,(count($Plots)+1)/2,$C_GraphLo);

         $Index       = count($Plots);
         $ColorFactor = -20 + ($SpliceHeight - $i) * $ColorRatio;
         $this->drawAntialiasPixel($Plots[0],$Plots[1],$this->Palette[$Key]["R"]+$ColorFactor,$this->Palette[$Key]["G"]+$ColorFactor,$this->Palette[$Key]["B"]+$ColorFactor);
         $this->drawAntialiasPixel($Plots[2],$Plots[3],$this->Palette[$Key]["R"]+$ColorFactor,$this->Palette[$Key]["G"]+$ColorFactor,$this->Palette[$Key]["B"]+$ColorFactor);
         $this->drawAntialiasPixel($Plots[$Index-4],$Plots[$Index-3],$this->Palette[$Key]["R"]+$ColorFactor,$this->Palette[$Key]["G"]+$ColorFactor,$this->Palette[$Key]["B"]+$ColorFactor);
        }
      }

     /* Draw Top polygons */
     for($Key=count($iValues)-1;$Key>=0;$Key--)
      { 
       $C_GraphLo = AllocateColor($this->Picture,$this->Palette[$Key]["R"],$this->Palette[$Key]["G"],$this->Palette[$Key]["B"]);
       imagefilledpolygon($this->Picture,$TopPlots[$Key],(count($TopPlots[$Key])+1)/2,$C_GraphLo);

       if ( $EnhanceColors ) { $En = 10; } else { $En = 5; }
       for($j=0;$j<=count($TopPlots[$Key])-4;$j=$j+2)
        $this->drawLine($TopPlots[$Key][$j],$TopPlots[$Key][$j+1],$TopPlots[$Key][$j+2],$TopPlots[$Key][$j+3],$this->Palette[$Key]["R"]+$En,$this->Palette[$Key]["G"]+$En,$this->Palette[$Key]["B"]+$En);
      }
    }

   /* This function can be used to set the background color */
   function drawBackground($R,$G,$B)
    {
     if ( $R < 0 ) { $R = 0; } if ( $R > 255 ) { $R = 255; }
     if ( $G < 0 ) { $G = 0; } if ( $G > 255 ) { $G = 255; }
     if ( $B < 0 ) { $B = 0; } if ( $B > 255 ) { $B = 255; }

     $C_Background = imagecolorallocate($this->Picture,$R,$G,$B);
     imagefilledrectangle($this->Picture,0,0,$this->XSize,$this->YSize,$C_Background);
    }

   /* This function create a rectangle with antialias */
   function drawRectangle($X1,$Y1,$X2,$Y2,$R,$G,$B)
    {
     if ( $R < 0 ) { $R = 0; } if ( $R > 255 ) { $R = 255; }
     if ( $G < 0 ) { $G = 0; } if ( $G > 255 ) { $G = 255; }
     if ( $B < 0 ) { $B = 0; } if ( $B > 255 ) { $B = 255; }

     $C_Rectangle = imagecolorallocate($this->Picture,$R,$G,$B);

     $X1=$X1-.2;$Y1=$Y1-.2;
     $X2=$X2+.2;$Y2=$Y2+.2;
     $this->drawLine($X1,$Y1,$X2,$Y1,$R,$G,$B);
     $this->drawLine($X2,$Y1,$X2,$Y2,$R,$G,$B);
     $this->drawLine($X2,$Y2,$X1,$Y2,$R,$G,$B);
     $this->drawLine($X1,$Y2,$X1,$Y1,$R,$G,$B);
    }

   /* This function create a filled rectangle with antialias */
   function drawFilledRectangle($X1,$Y1,$X2,$Y2,$R,$G,$B,$DrawBorder=TRUE)
    {
     if ( $R < 0 ) { $R = 0; } if ( $R > 255 ) { $R = 255; }
     if ( $G < 0 ) { $G = 0; } if ( $G > 255 ) { $G = 255; }
     if ( $B < 0 ) { $B = 0; } if ( $B > 255 ) { $B = 255; }

     $C_Rectangle = imagecolorallocate($this->Picture,$R,$G,$B);

     imagefilledrectangle($this->Picture,$X1,$Y1,$X2,$Y2,$C_Rectangle);

     if ( $DrawBorder )
      $this->drawRectangle($X1,$Y1,$X2,$Y2,$R,$G,$B);
    }

   /* This function create a rectangle with rounded corners and antialias */
   function drawRoundedRectangle($X1,$Y1,$X2,$Y2,$Radius,$R,$G,$B)
    {
     if ( $R < 0 ) { $R = 0; } if ( $R > 255 ) { $R = 255; }
     if ( $G < 0 ) { $G = 0; } if ( $G > 255 ) { $G = 255; }
     if ( $B < 0 ) { $B = 0; } if ( $B > 255 ) { $B = 255; }

     $C_Rectangle = imagecolorallocate($this->Picture,$R,$G,$B);

     $Step = 90 / ((3.1418 * $Radius)/2);

     for($i=0;$i<=90;$i=$i+$Step)
      {
       $X = cos(($i+180)*3.1418/180) * $Radius + $X1 + $Radius;
       $Y = sin(($i+180)*3.1418/180) * $Radius + $Y1 + $Radius;
       $this->drawAntialiasPixel($X,$Y,$R,$G,$B);

       $X = cos(($i-90)*3.1418/180) * $Radius + $X2 - $Radius;
       $Y = sin(($i-90)*3.1418/180) * $Radius + $Y1 + $Radius;
       $this->drawAntialiasPixel($X,$Y,$R,$G,$B);

       $X = cos(($i)*3.1418/180) * $Radius + $X2 - $Radius;
       $Y = sin(($i)*3.1418/180) * $Radius + $Y2 - $Radius;
       $this->drawAntialiasPixel($X,$Y,$R,$G,$B);

       $X = cos(($i+90)*3.1418/180) * $Radius + $X1 + $Radius;
       $Y = sin(($i+90)*3.1418/180) * $Radius + $Y2 - $Radius;
       $this->drawAntialiasPixel($X,$Y,$R,$G,$B);
      }

     $X1=$X1-.2;$Y1=$Y1-.2;
     $X2=$X2+.2;$Y2=$Y2+.2;
     $this->drawLine($X1+$Radius,$Y1,$X2-$Radius,$Y1,$R,$G,$B);
     $this->drawLine($X2,$Y1+$Radius,$X2,$Y2-$Radius,$R,$G,$B);
     $this->drawLine($X2-$Radius,$Y2,$X1+$Radius,$Y2,$R,$G,$B);
     $this->drawLine($X1,$Y2-$Radius,$X1,$Y1+$Radius,$R,$G,$B);
    }

   /* This function create a filled rectangle with rounded corners and antialias */
   function drawFilledRoundedRectangle($X1,$Y1,$X2,$Y2,$Radius,$R,$G,$B)
    {
     if ( $R < 0 ) { $R = 0; } if ( $R > 255 ) { $R = 255; }
     if ( $G < 0 ) { $G = 0; } if ( $G > 255 ) { $G = 255; }
     if ( $B < 0 ) { $B = 0; } if ( $B > 255 ) { $B = 255; }

     $C_Rectangle = imagecolorallocate($this->Picture,$R,$G,$B);

     $Step = 90 / ((3.1418 * $Radius)/2);

     for($i=0;$i<=90;$i=$i+$Step)
      {
       $Xi1 = cos(($i+180)*3.1418/180) * $Radius + $X1 + $Radius;
       $Yi1 = sin(($i+180)*3.1418/180) * $Radius + $Y1 + $Radius;

       $Xi2 = cos(($i-90)*3.1418/180) * $Radius + $X2 - $Radius;
       $Yi2 = sin(($i-90)*3.1418/180) * $Radius + $Y1 + $Radius;

       $Xi3 = cos(($i)*3.1418/180) * $Radius + $X2 - $Radius;
       $Yi3 = sin(($i)*3.1418/180) * $Radius + $Y2 - $Radius;

       $Xi4 = cos(($i+90)*3.1418/180) * $Radius + $X1 + $Radius;
       $Yi4 = sin(($i+90)*3.1418/180) * $Radius + $Y2 - $Radius;

       imageline($this->Picture,$Xi1+1,$Yi1,$X1+$Radius,$Yi1,$C_Rectangle);
       imageline($this->Picture,$X2-$Radius,$Yi2,$Xi2+1,$Yi2,$C_Rectangle);
       imageline($this->Picture,$X2-$Radius,$Yi3,$Xi3+1,$Yi3,$C_Rectangle);
       imageline($this->Picture,$Xi4+1,$Yi4,$X1+$Radius,$Yi4,$C_Rectangle);

       $this->drawAntialiasPixel($Xi1,$Yi1,$R,$G,$B);
       $this->drawAntialiasPixel($Xi2,$Yi2,$R,$G,$B);
       $this->drawAntialiasPixel($Xi3,$Yi3,$R,$G,$B);
       $this->drawAntialiasPixel($Xi4,$Yi4,$R,$G,$B);
      }

     imagefilledrectangle($this->Picture,$X1,$Y1+$Radius,$X2,$Y2-$Radius,$C_Rectangle);
     imagefilledrectangle($this->Picture,$X1+$Radius,$Y1,$X2-$Radius,$Y2,$C_Rectangle);

     $X1=$X1-.2;$Y1=$Y1-.2;
     $X2=$X2+.2;$Y2=$Y2+.2;
     $this->drawLine($X1+$Radius,$Y1,$X2-$Radius,$Y1,$R,$G,$B);
     $this->drawLine($X2,$Y1+$Radius,$X2,$Y2-$Radius,$R,$G,$B);
     $this->drawLine($X2-$Radius,$Y2,$X1+$Radius,$Y2,$R,$G,$B);
     $this->drawLine($X1,$Y2-$Radius,$X1,$Y1+$Radius,$R,$G,$B);
    }

   /* This function create a circle with antialias */
   function drawCircle($Xc,$Yc,$Height,$R,$G,$B,$Width=0)
    {
     if ( $Width == 0 ) { $Width = $Height; }
     if ( $R < 0 ) { $R = 0; } if ( $R > 255 ) { $R = 255; }
     if ( $G < 0 ) { $G = 0; } if ( $G > 255 ) { $G = 255; }
     if ( $B < 0 ) { $B = 0; } if ( $B > 255 ) { $B = 255; }

     $C_Circle = imagecolorallocate($this->Picture,$R,$G,$B);
     $Step     = 360 / (2 * 3.1418 * max($Width,$Height));

     for($i=0;$i<=360;$i=$i+$Step)
      {
       $X = cos($i*3.1418/180) * $Height + $Xc;
       $Y = sin($i*3.1418/180) * $Width + $Yc;
       $this->drawAntialiasPixel($X,$Y,$R,$G,$B);
      }
    }

   /* This function create a filled circle/ellipse with antialias */
   function drawFilledCircle($Xc,$Yc,$Height,$R,$G,$B,$Width=0)
    {
     if ( $Width == 0 ) { $Width = $Height; }
     if ( $R < 0 ) { $R = 0; } if ( $R > 255 ) { $R = 255; }
     if ( $G < 0 ) { $G = 0; } if ( $G > 255 ) { $G = 255; }
     if ( $B < 0 ) { $B = 0; } if ( $B > 255 ) { $B = 255; }

     $C_Circle = imagecolorallocate($this->Picture,$R,$G,$B);
     $Step     = 360 / (2 * 3.1418 * max($Width,$Height));

     for($i=90;$i<=270;$i=$i+$Step)
      {
       $X1 = cos($i*3.1418/180) * $Height + $Xc;
       $Y1 = sin($i*3.1418/180) * $Width + $Yc;
       $X2 = cos((180-$i)*3.1418/180) * $Height + $Xc;
       $Y2 = sin((180-$i)*3.1418/180) * $Width + $Yc;

       $this->drawAntialiasPixel($X1-1,$Y1-1,$R,$G,$B);
       $this->drawAntialiasPixel($X2-1,$Y2-1,$R,$G,$B);

       if ( ($Y1-1) > $Yc - max($Width,$Height) )
        imageline($this->Picture,$X1,$Y1-1,$X2-1,$Y2-1,$C_Circle);
      }
    }

   /* This function will draw a filled ellipse */
   function drawEllipse($Xc,$Yc,$Height,$Width,$R,$G,$B)
    { $this->drawCircle($Xc,$Yc,$Height,$R,$G,$B,$Width); }

   /* This function will draw an ellipse */
   function drawFilledEllipse($Xc,$Yc,$Height,$Width,$R,$G,$B)
    { $this->drawFilledCircle($Xc,$Yc,$Height,$R,$G,$B,$Width); }

   /* This function create a line with antialias */
   function drawLine($X1,$Y1,$X2,$Y2,$R,$G,$B,$GraphFunction=FALSE)
    {
     if ( $this->LineDotSize > 1 ) { $this->drawDottedLine($X1,$Y1,$X2,$Y2,$this->LineDotSize,$R,$G,$B,$GraphFunction); return(0); }
     if ( $R < 0 ) { $R = 0; } if ( $R > 255 ) { $R = 255; }
     if ( $G < 0 ) { $G = 0; } if ( $G > 255 ) { $G = 255; }
     if ( $B < 0 ) { $B = 0; } if ( $B > 255 ) { $B = 255; }

     $Distance = sqrt(($X2-$X1)*($X2-$X1)+($Y2-$Y1)*($Y2-$Y1));  
     if ( $Distance == 0 )
      return(-1);
     $XStep = ($X2-$X1) / $Distance;
     $YStep = ($Y2-$Y1) / $Distance;

     for($i=0;$i<=$Distance;$i++)
      {
       $X = $i * $XStep + $X1;
       $Y = $i * $YStep + $Y1;

       if ( ($X >= $this->GArea_X1 && $X <= $this->GArea_X2 && $Y >= $this->GArea_Y1 && $Y <= $this->GArea_Y2) || !$GraphFunction )
        {
         if ( $this->LineWidth == 1 )
          $this->drawAntialiasPixel($X,$Y,$R,$G,$B);
         else
          {
           $StartOffset = -($this->LineWidth/2); $EndOffset = ($this->LineWidth/2);
           for($j=$StartOffset;$j<=$EndOffset;$j++)
            $this->drawAntialiasPixel($X+$j,$Y+$j,$R,$G,$B);
          }
        }
      }
    }

   /* This function create a line with antialias */
   function drawDottedLine($X1,$Y1,$X2,$Y2,$DotSize,$R,$G,$B,$GraphFunction=FALSE)
    {
     if ( $R < 0 ) { $R = 0; } if ( $R > 255 ) { $R = 255; }
     if ( $G < 0 ) { $G = 0; } if ( $G > 255 ) { $G = 255; }
     if ( $B < 0 ) { $B = 0; } if ( $B > 255 ) { $B = 255; }

     $Distance = sqrt(($X2-$X1)*($X2-$X1)+($Y2-$Y1)*($Y2-$Y1));  

     $XStep = ($X2-$X1) / $Distance;
     $YStep = ($Y2-$Y1) / $Distance;

     $DotIndex = 0;
     for($i=0;$i<=$Distance;$i++)
      {
       $X = $i * $XStep + $X1;
       $Y = $i * $YStep + $Y1;

       if ( $DotIndex <= $DotSize)
        {
         if ( ($X >= $this->GArea_X1 && $X <= $this->GArea_X2 && $Y >= $this->GArea_Y1 && $Y <= $this->GArea_Y2) || !$GraphFunction )
          {
           if ( $this->LineWidth == 1 )
            $this->drawAntialiasPixel($X,$Y,$R,$G,$B);
           else
            {
             $StartOffset = -($this->LineWidth/2); $EndOffset = ($this->LineWidth/2);
             for($j=$StartOffset;$j<=$EndOffset;$j++)
              $this->drawAntialiasPixel($X+$j,$Y+$j,$R,$G,$B);
            }
          }
        }

       $DotIndex++;
       if ( $DotIndex == $DotSize * 2 )
        $DotIndex = 0;        
      }
    }

   /* Load a PNG file and draw it over the chart */
   function drawFromPNG($FileName,$X,$Y,$Alpha=100)
    { $this->drawFromPicture(1,$FileName,$X,$Y,$Alpha); }

   /* Load a GIF file and draw it over the chart */
   function drawFromGIF($FileName,$X,$Y,$Alpha=100)
    { $this->drawFromPicture(2,$FileName,$X,$Y,$Alpha); }

   /* Load a JPEG file and draw it over the chart */
   function drawFromJPG($FileName,$X,$Y,$Alpha=100)
    { $this->drawFromPicture(3,$FileName,$X,$Y,$Alpha); }

   function drawFromPicture($PicType,$FileName,$X,$Y,$Alpha=100)
    {
     if ( file_exists($FileName))
      {
       $Infos  = getimagesize($FileName);
       $Width  = $Infos[0];
       $Height = $Infos[1];
       if ( $PicType == 1 ) { $Raster = imagecreatefrompng($FileName); }
       if ( $PicType == 2 ) { $Raster = imagecreatefromgif($FileName); }
       if ( $PicType == 3 ) { $Raster = imagecreatefromjpeg($FileName); }

       imagecopymerge($this->Picture,$Raster,$X,$Y,0,0,$Width,$Height,$Alpha);
       imagedestroy($Raster);
      }
    }

   /* Draw an alpha pixel */
   function drawAlphaPixel($X,$Y,$Alpha,$R,$G,$B)
    {
     if ( $R < 0 ) { $R = 0; } if ( $R > 255 ) { $R = 255; }
     if ( $G < 0 ) { $G = 0; } if ( $G > 255 ) { $G = 255; }
     if ( $B < 0 ) { $B = 0; } if ( $B > 255 ) { $B = 255; }

     if ( $X < 0 || $Y < 0 || $X >= $this->XSize || $Y >= $this->YSize )
      return(-1);

     $RGB2 = imagecolorat($this->Picture, $X, $Y);
     $R2   = ($RGB2 >> 16) & 0xFF;
     $G2   = ($RGB2 >> 8) & 0xFF;
     $B2   = $RGB2 & 0xFF;

     $iAlpha = (100 - $Alpha)/100;
     $Alpha  = $Alpha / 100;

     $Ra   = floor($R*$Alpha+$R2*$iAlpha);
     $Ga   = floor($G*$Alpha+$G2*$iAlpha);
     $Ba   = floor($B*$Alpha+$B2*$iAlpha);

     $C_Aliased = imagecolorallocate($this->Picture,$Ra,$Ga,$Ba);
     imagesetpixel($this->Picture,$X,$Y,$C_Aliased);
    }

   /* Render the current picture to a file */
   function Render($FileName)
    {
     imagepng($this->Picture,$FileName);
    }

   /* Render the current picture to STDOUT */
   function Stroke()
    {
     header('Content-type: image/png');
     //header('Content-Length: ' . strlen($this->Picture));
     imagepng($this->Picture);
    }

   /* Private functions for internal processing */
   function drawAntialiasPixel($X,$Y,$R,$G,$B)
    {
     if ( $R < 0 ) { $R = 0; } if ( $R > 255 ) { $R = 255; }
     if ( $G < 0 ) { $G = 0; } if ( $G > 255 ) { $G = 255; }
     if ( $B < 0 ) { $B = 0; } if ( $B > 255 ) { $B = 255; }

     $Plot = "";
     $Xi   = floor($X);
     $Yi   = floor($Y);

     if ( $Xi == $X && $Yi == $Y)
      {
       /* $this->drawAlphaPixel($Xi,$Yi,0,$R,$G,$B); */
       $C_Aliased = imagecolorallocate($this->Picture,$R,$G,$B);
       imagesetpixel($this->Picture,$X,$Y,$C_Aliased);
      }
     else
      {
       $Alpha1 = (1 - ($X - floor($X))) * (1 - ($Y - floor($Y))) * 100;
       if ( $Alpha1 > $this->AntialiasQuality ) { $this->drawAlphaPixel($Xi,$Yi,$Alpha1,$R,$G,$B); }

       $Alpha2 = ($X - floor($X)) * (1 - ($Y - floor($Y))) * 100;
       if ( $Alpha2 > $this->AntialiasQuality ) { $this->drawAlphaPixel($Xi+1,$Yi,$Alpha2,$R,$G,$B); }

       $Alpha3 = (1 - ($X - floor($X))) * ($Y - floor($Y)) * 100;
       if ( $Alpha3 > $this->AntialiasQuality ) { $this->drawAlphaPixel($Xi,$Yi+1,$Alpha3,$R,$G,$B); }

       $Alpha4 = ($X - floor($X)) * ($Y - floor($Y)) * 100;
       if ( $Alpha4 > $this->AntialiasQuality ) { $this->drawAlphaPixel($Xi+1,$Yi+1,$Alpha4,$R,$G,$B); }
      }
    }
  }

 function AllocateColor($Picture,$R,$G,$B,$Factor=0)
  {
   $R = $R + $Factor;
   $G = $G + $Factor;
   $B = $B + $Factor;
   if ( $R < 0 ) { $R = 0; } if ( $R > 255 ) { $R = 255; }
   if ( $G < 0 ) { $G = 0; } if ( $G > 255 ) { $G = 255; }
   if ( $B < 0 ) { $B = 0; } if ( $B > 255 ) { $B = 255; }

   return(imagecolorallocate($Picture,$R,$G,$B));
  }

 function RaiseFatal($Message)
  {
   echo "[FATAL] ".$Message."\r\n";
   exit();
  }
?>