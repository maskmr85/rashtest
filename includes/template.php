<?php
/***************************************************************************
 *                                  Rash CMS
 *                          -------------------
 *   copyright            : (C) 2009 The RashCMS  $Team = "www.rashcms.com";
 *   email                : info@rashcms.com
 *   email                : rashcms@gmail.com
 *   programmer           : Reza Shahrokhian
 ***************************************************************************/
//	 Security
if ( !defined('news_security'))
{
 die("You are not allowed to access this page directly!");
}
class RashCMS
{
   var $tags = array();
   var $required_tags = array();
   var $blocks = array();
   var $tpl = '';
   var $parsed_tpl = '';

   function load($tpl=''){
 		if( !empty($tpl) )
      	{
         if(!file_exists($tpl))
         {
            $this->tplerror('Could not find the template file <b>'. $tpl ."</b>");
            return FALSE;
         }
         $tpl = @implode( '',@file($tpl));
         $tpl = $tpl ? $tpl : $this->tplerror('Could not read the template file!');
         $this->tpl = $tpl;
      	}
	}
	function tplerror($error)
	{
	echo $error;
	return FALSE;
	}
   function assign( $input, $value = '', $required = FALSE )
   {
      if( is_array( $input ) )
      {
         foreach( $input as $tag => $value )
         {
            if(empty( $tag ) )
               $this->tplerror('R a s h C M S::The tag name shouldnt be empty.');
            if( $required == TRUE )
            {
               $this->required_tags[$tag] = $value;
            }
            else
            {
               $this->tags[$tag] = $value;
            }
         }
      }
      elseif( is_string( $input ) )
      {
         if( empty( $input ) )
			$this->tplerror('R a s h C M S::The tag name shouldnt be empty.');
         else
         {
            if( $required == TRUE )
            {
               $this->required_tags[$input] = $value;
            }
            else
            {
               $this->tags[$input] = $value;
            }
         }
      }
      else
      {
         return FALSE;
      }
      return TRUE;
   }

   /*-----------------------------------------------------------------*/

   function block($block_name, $block_array)
   {
      if( !is_string($block_name) || empty($block_name))
      $this->tplerror('R a s h C M S::Block name is not a string or is empty!');

      if( !is_array($block_array))
        $this->tplerror('R a s h C M S::Block array is not an array!');
        $this->blocks[$block_name][] = $block_array;
   }

   /*-----------------------------------------------------------------*/

   function parse()
   {
      if( empty( $this->tpl ) )
      {
         return;
      }

      # blocks
      $tmp_blocknames = array();
      foreach( $this->blocks as $block_name => $block_arrays )
      {
         if( $anzahl = preg_match_all( '/<tag:'. preg_quote( $block_name, '/' ) .'>(.*)<\/tag:'. preg_quote( $block_name, '/' ) .'>/sU', $this->tpl, $matches ) )
         {
            for( $i = 0; $i < $anzahl; $i++ )
            {
               $block_plus_definition = $matches[0][$i];
               $block = $matches[1][$i];

               if( is_int( strpos( $block, '<!-- IF' ) ) )
               {
                  $parse_control_structures = TRUE;
               }

               $parsed_block = '';
               foreach( $block_arrays as $block_array )
               {
                  $tmp = $block;
                  if( isset( $parse_control_structures ) )
                  {
                     $tmp = $this->_parse_control_structures( $tmp, array_merge( $block_array, $this->tags, $this->required_tags ) );
                  }
                  foreach( $block_array as $tag_name => $tag_value )
                  {
                     $tmp = str_replace( '['.$tag_name.']', $tag_value, $tmp );
                  }
                  $parsed_block .= $tmp;
               }
               $this->tpl = str_replace( $block_plus_definition, $parsed_block, $this->tpl );
               $tmp_blocknames[] = $block_name;
               unset( $parse_control_structures );
            }
         }
      }
      if( count( $this->blocks ) > 0 )
      {
         $this->tpl = preg_replace( "/<(tag|\/tag):(". implode( '|', $tmp_blocknames ) .")>/", '', $this->tpl );
      }
      # unbenutze blcke entfernen
      $this->tpl = preg_replace( "/<tag:([a-zA-Z0-9_-]+)>.*<\/tag:\\1>( |\r|\n)?/msU", '', $this->tpl );

      # single tags
      foreach( $this->required_tags as $tag_name => $tag_value )
      {
         if( !is_int( strpos( $this->tpl, $tag_name ) ) )
         $this->tplerror('R a s h C M S::Could not find tag <i>'.$tag_name.'</i> in the template file!');
         else
         {
            $this->tpl = str_replace( '['.$tag_name.']', $tag_value, $this->tpl );
         }
      }
      foreach( $this->tags as $tag_name => $tag_value )
      {
         $this->tpl = str_replace( '['.$tag_name.']', $tag_value, $this->tpl );
      }

      # if & else
      $this->tpl = $this->_parse_control_structures(
         $this->tpl,
         array_merge( $this->tags, $this->required_tags ),
         $this->blocks
      );


      $this->parsed_tpl = $this->tpl;
      $this->tpl = '';
   }

   /*-----------------------------------------------------------------*/

   function showit()
   {
      if( !empty( $this->tpl ) )
      {
         $this->parse();
      }
      print $this->parsed_tpl;
   }

   /*-----------------------------------------------------------------*/

   function dontshowit()
   {
      if( !empty( $this->tpl ) )
      {
         $this->parse();
      }
      return $this->parsed_tpl;
   }

   /*-----------------------------------------------------------------*/

   function reset()
   {
      $this->tpl = '';
      $this->parsed_tpl = '';
      $this->tags = array();
      $this->required_tags = array();
      $this->blocks = array();
   }

   /*-----------------------------------------------------------------*/

   function _parse_control_structures( $tpl, $vars, $blocks = array() )
   {
      if( $matchnumber = preg_match_all( '/<!-- IF (!?)((BLOCK )?)([_a-zA-Z0-9\-]+) -->(.*)((<!-- ELSEIF !\(\\1\\2\\4\) -->)(.*))?<!-- ENDIF \\1\\2\\4 -->/msU', $tpl, $matches ) )
      {
         for( $i = 0; $i < $matchnumber; $i++ )
         {
            //print( $matches[8 ][$i] . '<br />');
            if( !empty( $matches[2][$i] ) )
            {
               $code = 'if( '.$matches[1][$i].'isset($blocks[\''.$matches[4][$i].'\']) )'."\n";
            }
            else
            {
               $code = 'if( '.$matches[1][$i].'( isset($vars[\''.$matches[4][$i].'\']) ) )'."\n";
            }
            $code .= '{ $tpl = str_replace( $matches[0][$i], $this->_parse_control_structures( $matches[5][$i], $vars, $blocks ), $tpl ); }'."\n";
            $code .= ' else '."\n";
            $code .= '{ $tpl = str_replace( $matches[0][$i], !empty($matches[7][$i]) ? $this->_parse_control_structures( $matches[8][$i], $vars, $blocks ) : \'\', $tpl ); }';
            eval( $code );
         }
      }
      return $tpl;
   }
}
?>