<?php
/* qr
 * ~ qr code generator
 * ~ extension for AI
 * authored by 9r3i
 * https://github.com/9r3i
 * started at august 26th 2019
 * require: qrLibs (AI package)
 */
class qr{
  const version='1.0.0';
  const info='QR code generator.';
  private $loadedLibs=false;
  /* generate text to png
   * @parameters:
   *   $text   = string of text to be generated
   *   $output = string of output file; default: output.png
   *   $level  = int of level (0, 1, 2, 3); default: 3
   *   $size   = int of pixel size; default: 7
   *   $margin = int of margin; default: 5
   * @result: string of output file on success; string of error message on failed
   */
  function png(string $text='',string $output='output.png',$level=3,$size=7,$margin=5){
    /* check text */
    if(empty($text)){
      return ai::error('Require string text');
    }
    /* load qr library */
    if(!$this->qrLibsLoad()){
      return ai::error('Failed to load QR library.');
    }
    /* prepare output file */
    $output=is_string($output)?$output:'output.png';
    /* prepare level */
    $level=preg_match('/^[0-3]$/',$level)?intval($level):3;
    /* prepare size */
    $size=preg_match('/^\d+$/',$size)?intval($size):7;
    /* prepare margin */
    $margin=preg_match('/^\d+$/',$margin)?intval($margin):5;
    /* generate qr image/png */
    QRcode::png($text,$output,$level,$size,$margin);
    /* return output file */
    return $output;
  }
  /* load qrLibs */
  private function qrLibsLoad(){
    /* check loaded library */
    if($this->loadedLibs){return true;}
    /* prepare require loaded files */
    $requireFiles=[
      "qrLibs/qrconst.php",
      "qrLibs/qrconfig.php",
      "qrLibs/qrtools.php",
      "qrLibs/qrspec.php",
      "qrLibs/qrimage.php",
      "qrLibs/qrinput.php",
      "qrLibs/qrbitstream.php",
      "qrLibs/qrsplit.php",
      "qrLibs/qrrscode.php",
      "qrLibs/qrmask.php",
      "qrLibs/qrencode.php",
      "qrLibs/qrarea.php",
      "qrLibs/qrcanvas.php",
      "qrLibs/qrsvg.php"
    ];
    /* load each */
    foreach($requireFiles as $requireFile){
      require_once(LIBDIR.$requireFile);
    }
    /* set loaded library */
    if(!class_exists('QRcode',false)){
      return false;
    }
    /* set loaded library */
    $this->loadedLibs=true;
    /* return as true */
    return true;
  }
  /* get help */
  function help(){
    $info=$this::info;
    $version=$this::version;
    return <<<EOD
{$info}
Version {$version}

  $ AI QR <option> <arguments>

Options:
  PNG    Generate text to png.

Scheme:
  $ AI QR PNG <text> [out:output.png] [level:3] [size:7] [margin:5]
  
Example:
  $ AI QR PNG "https://github.com/9r3i/qr-libs"
EOD;
  }
}


