<?php
/* qr
 * ~ qr code generator
 * ~ qr code detector as version 2.0.0 (july 24th 2020)
 * ~ extension for AI
 * authored by 9r3i
 * https://github.com/9r3i
 * started at august 26th 2019
 * require: - qrLibs (AI package)
 *          - Zxing (july 24th 2020)
 *          - ext.image (commercial package)
 */
class qr{
  const version='2.1.0';
  const info='QR code generator and detector.';
  private $loadedLibs=false;
  /* read qr-file */
  public function read(string $file){
    /* check the given file */
    if(!is_file($file)){
      return ai::error('Require image file.');
    }
    /* check file type -- must be image/png */
    if(!preg_match('/\.png$/i',$file)){
      /* check image */
      if(!preg_match('/\.(jpg|jpeg|gif|bmp|webp)$/i',$file,$akur)){
        return ai::error('Invalid file, the file must be image.');
      }
      /* load image extension */
      if(!ai::loadExts('image')){
        return "NOTICE: Require ext.image to read besides png file.\r\n"
              ."        To install: $ ai install ext.image";
      }
      /* export other type to png */
      ai::interactiveSet(true);
      $outFile=(new image)->export($file,'png');
      ai::interactiveSet(false);
      if(!$outFile||!is_file($outFile)){
        return ai::error('Failed to export image file.');
      }
      /* set new file */
      $file=$outFile;
    }
    /* load required functions */
    require_once(LIBDIR.'Zxing/Common/customFunctions.php');
    /* get string text */
    $output=(new \Zxing\QrReader($file))->text();
    /* remove output file */
    if(isset($outFile)&&is_file($outFile)){
      @unlink($outFile);
    }
    /* return output */
    return $output;
  }
  /* generate text to jpg */
  public function jpg(string $text='',string $output='output.jpg',$level=3,$size=7,$margin=5){
    /* load image extension */
    if(!ai::loadExts('image')){
      return "NOTICE: Require ext.image to create besides png file.\r\n"
            ."        To install: $ ai install ext.image";
    }
    /* generate png first */
    if(!preg_match('/\.(jpg|jpeg)$/i',$output)){
      return ai::error('Output file must be jpg or jpeg.');
    }
    /* generate png first */
    $pngOutput=preg_replace('/\.(jpg|jpeg)$/i','.png',$output);
    /* generate png first */
    $toPNG=$this->png($text,$pngOutput,$level,$size,$margin);
    /* export to jpg */
    ai::interactiveSet(true);
    $jpgFile=(new image)->export($pngOutput,'jpg');
    ai::interactiveSet(false);
    /* remove png output */
    @unlink($pngOutput);
    /* check jpg file */
    if(!$jpgFile||!is_file($jpgFile)){
      return ai::error('Failed to export image file.');
    }
    /* rename to output file name */
    @rename($jpgFile,$output);
    /* return output file name */
    return $output;
  }
  /* generate text to png
   * @parameters:
   *   $text   = string of text to be generated
   *   $output = string of output file; default: output.png
   *   $level  = int of level (0, 1, 2, 3); default: 3
   *   $size   = int of pixel size; default: 7
   *   $margin = int of margin; default: 5
   * @result: string of output file on success; string of error message on failed
   */
  public function png(string $text='',string $output='output.png',$level=3,$size=7,$margin=5){
    /* check text */
    if(empty($text)){
      return ai::error('Require string text.');
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
    /* return output file name */
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
  public function help(){
    $info=$this::info;
    $version=$this::version;
    return <<<EOD
{$info}
Version {$version}

  $ AI QR <option> <arguments>

Options:
  PNG    Generate text to png.
  JPG    Generate text to jpg.
  READ   Read QR-file from image file.

Scheme:
  $ AI QR PNG <text> [out:output.png] [level:3] [size:7] [margin:5]
  $ AI QR JPG <text> [out:output.jpg] [level:3] [size:7] [margin:5]
  $ AI QR READ <path/to/file.png>
  
Example:
  $ AI QR PNG "https://github.com/9r3i/qr-libs"
EOD;
  }
}


