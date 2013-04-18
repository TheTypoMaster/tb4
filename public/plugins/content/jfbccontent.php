<?php
/**
 * @package        JFBConnect
 * @copyright (C) 2009-2012 by Source Coast - All rights reserved
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.event.plugin');
 //SC16
jimport('sourcecoast.utilities');
jimport('sourcecoast.articleContent');

class plgContentJFBCContent extends JPlugin
{

    function onBeforeDisplayContent(&$article, &$params, $limitstart)
    {
        $this->onContentBeforeDisplay('SC15', $article, $params, $limitstart);
    }

    function onContentBeforeDisplay($context, &$article, &$params, $limitstart = 0)
    {
        $app = JFactory::getApplication();
        if ($app->isAdmin())
        {
            return;
        }

        //Get Social RenderKey
        jimport('joomla.filesystem.file');
        $libFile = JPATH_ROOT . DS . 'components' . DS . 'com_jfbconnect' . DS . 'libraries' . DS . 'facebook.php';
        if (!JFile::exists($libFile))
            return;

        require_once($libFile);
        $jfbcLibrary = JFBConnectFacebookLibrary::getInstance();
        $renderKey = $jfbcLibrary->getSocialTagRenderKey();
        if ($renderKey)
            $renderKeyString = " key=" . $renderKey;
        else
            $renderKeyString = "";

        $configModel = $jfbcLibrary->getConfigModel();

        $view = JRequest::getVar('view');
        $layout = JRequest::getVar('layout');
        $task = JRequest::getVar('task');
        $isArticleView = SCArticleContent::isArticleView($view);

        if ($view == 'item' || $view == 'itemlist' || $view == 'latest') //K2
        {
            $showK2Comments = SCArticleContent::showSocialItemInK2Item($article,
                                                            $configModel->getSetting('social_k2_comment_item_include_ids'),
                                                            $configModel->getSetting('social_k2_comment_item_exclude_ids'),
                                                            $configModel->getSetting('social_k2_comment_cat_include_type'),
                                                            $configModel->getSetting('social_k2_comment_cat_ids'));

            $showK2Like = SCArticleContent::showSocialItemInK2Item($article,
                                                        $configModel->getSetting('social_k2_like_item_include_ids'),
                                                        $configModel->getSetting('social_k2_like_item_exclude_ids'),
                                                        $configModel->getSetting('social_k2_like_cat_include_type'),
                                                        $configModel->getSetting('social_k2_like_cat_ids'));

            $showK2CommentsInViewPosition = SCArticleContent::getSocialK2ItemViewPosition($article, $view, $layout, $task,
                                                                               $configModel->getSetting('social_k2_comment_item_view'),
                                                                               $configModel->getSetting('social_k2_comment_tag_view'),
                                                                               $configModel->getSetting('social_k2_comment_category_view'),
                                                                               $configModel->getSetting('social_k2_comment_userpage_view'),
                                                                               $configModel->getSetting('social_k2_comment_latest_view')
            );

            $showK2LikeInViewPosition = SCArticleContent::getSocialK2ItemViewPosition($article, $view, $layout, $task,
                                                                           $configModel->getSetting('social_k2_like_item_view'),
                                                                           $configModel->getSetting('social_k2_like_tag_view'),
                                                                           $configModel->getSetting('social_k2_like_category_view'),
                                                                           $configModel->getSetting('social_k2_like_userpage_view'),
                                                                           $configModel->getSetting('social_k2_like_latest_view')
            );
            if ($showK2Like == true && $showK2LikeInViewPosition != SC_VIEW_NONE)
            {
                if ($isArticleView) //Item View
                    $likeText = $this->_getK2ItemLike($article, $configModel, $renderKeyString, $showK2LikeInViewPosition);
                else //Blog View
                    $likeText = $this->_getK2BlogLike($article, $configModel, $renderKeyString, $showK2LikeInViewPosition);

                SCArticleContent::addTextToArticle($article, $likeText, $showK2LikeInViewPosition);
            }
            if ($showK2Comments == true && $showK2CommentsInViewPosition != SC_VIEW_NONE)
            {
                if ($isArticleView) //Item Text
                    $commentText = $this->_getK2ItemComments($article, $configModel, $renderKeyString);
                else
                    $commentText = $this->_getK2BlogComments($article, $configModel, $renderKeyString);

                SCArticleContent::addTextToArticle($article, $commentText, $showK2CommentsInViewPosition);
            }
        }
        else
        {
            $showComments = SCArticleContent::showSocialItemInArticle($article,
                                                           $configModel->getSetting('social_comment_article_include_ids'),
                                                           $configModel->getSetting('social_comment_article_exclude_ids'),
                                                           $configModel->getSetting('social_comment_cat_include_type'),
                                                           $configModel->getSetting('social_comment_cat_ids'),
                                                           $configModel->getSetting('social_comment_sect_include_type'),
                                                           $configModel->getSetting('social_comment_sect_ids'));

            $showLike = SCArticleContent::showSocialItemInArticle($article,
                                                       $configModel->getSetting('social_like_article_include_ids'),
                                                       $configModel->getSetting('social_like_article_exclude_ids'),
                                                       $configModel->getSetting('social_like_cat_include_type'),
                                                       $configModel->getSetting('social_like_cat_ids'),
                                                       $configModel->getSetting('social_like_sect_include_type'),
                                                       $configModel->getSetting('social_like_sect_ids'));

            $showCommentsInViewPosition = SCArticleContent::getSocialItemViewPosition($article, $view,
                                                                           $configModel->getSetting('social_comment_article_view'),
                                                                           $configModel->getSetting('social_comment_frontpage_view'),
                                                                           $configModel->getSetting('social_comment_category_view'),
                                                                           $configModel->getSetting('social_comment_section_view'));

            $showLikeInViewPosition = SCArticleContent::getSocialItemViewPosition($article, $view,
                                                                       $configModel->getSetting('social_like_article_view'),
                                                                       $configModel->getSetting('social_like_frontpage_view'),
                                                                       $configModel->getSetting('social_like_category_view'),
                                                                       $configModel->getSetting('social_like_section_view'));

            if ($showLike == true && $showLikeInViewPosition != SC_VIEW_NONE)
            {
                if ($isArticleView) //Article Text
                    $likeText = $this->_getJoomlaArticleLike($article, $configModel, $renderKeyString, $showLikeInViewPosition);
                else //Blog Text
                    $likeText = $this->_getJoomlaBlogLike($article, $configModel, $renderKeyString, $showLikeInViewPosition);

                SCArticleContent::addTextToArticle($article, $likeText, $showLikeInViewPosition);
            }
            if ($showComments == true && $showCommentsInViewPosition != SC_VIEW_NONE)
            {
                if ($isArticleView) //Article Text
                    $commentText = $this->_getJoomlaArticleComments($article, $configModel, $renderKeyString);
                else //Blog Text
                    $commentText = $this->_getJoomlaBlogComments($article, $configModel, $renderKeyString);

                SCArticleContent::addTextToArticle($article, $commentText, $showCommentsInViewPosition);
            }
        }
        $socialGraphEnabled = $configModel->getSetting('social_graph_enabled');

        //Add first image from article if enabled
        $socialGraphFirstImage = $configModel->getSetting('social_graph_first_image');
        if ($socialGraphEnabled == "1" && $socialGraphFirstImage == "1" && SCArticleContent::isArticleView($view))
        {
            $firstImage = '';

            //Attempt to get main image from a K2 article
            if($view == 'item')
                $firstImage = SCArticleContent::getK2MainImage($article);

            //Attempt to get the first image out of the article if we're not using a K2 main image
            if($firstImage == '')
                $firstImage = SCArticleContent::getFirstImage($article);

            //Add Open Graph tag if image was found
            if ($firstImage != '')
            {
                $graphTag = '{SCOpenGraph image=' . $firstImage . $renderKeyString . '}';
                SCArticleContent::addTextToArticle($article, $graphTag, "1"); //0=None, 1=Top, 2=Bottom, 3=Both
            }
        }

        //Add text from article if enabled
        $socialGraphFirstText = $configModel->getSetting('social_graph_first_text');
        if ($socialGraphEnabled == "1" && $socialGraphFirstText == "1" && SCArticleContent::isArticleView($view))
        {
            $socialGraphFirstTextLength = $configModel->getSetting('social_graph_first_text_length');
            $firstText = SCArticleContent::getFirstArticleText($article, $socialGraphFirstTextLength);

            //Add Open Graph tag if text was found
            if ($firstText != '')
            {
                $graphTag = '{SCOpenGraph description=' . $firstText . $renderKeyString . '}';
                SCArticleContent::addTextToArticle($article, $graphTag, "1"); //0=None, 1=Top, 2=Bottom, 3=Both
            }
        }
    }

    function _getJoomlaArticleLike($article, $configModel, $renderKeyString, $showLikeInViewPosition)
    {
        $buttonStyle = $configModel->getSetting('social_article_like_layout_style');
        $showFaces = $configModel->getSetting('social_article_like_show_faces');
        $showSendButton = $configModel->getSetting('social_article_like_show_send_button');
        $width = $configModel->getSetting('social_article_like_width');
        $verbToDisplay = $configModel->getSetting('social_article_like_verb_to_display');
        $font = $configModel->getSetting('social_article_like_font');
        $colorScheme = $configModel->getSetting('social_article_like_color_scheme');
        $showLinkedIn = $configModel->getSetting('social_article_like_show_linkedin');
        $showTwitter = $configModel->getSetting('social_article_like_show_twitter');
        $showGooglePlus = $configModel->getSetting('social_article_like_show_googleplus');

        $likeText = $this->_getLikeButton($article, $buttonStyle, $showFaces, $showSendButton, $showLinkedIn, $showTwitter, $showGooglePlus, $width, $verbToDisplay, $font, $colorScheme, $renderKeyString, $showLikeInViewPosition, true);
        return $likeText;
    }

    function _getJoomlaBlogLike($article, $configModel, $renderKeyString, $showLikeInViewPosition)
    {
        $buttonStyle = $configModel->getSetting('social_blog_like_layout_style');
        $showFaces = $configModel->getSetting('social_blog_like_show_faces');
        $showSendButton = $configModel->getSetting('social_blog_like_show_send_button');
        $width = $configModel->getSetting('social_blog_like_width');
        $verbToDisplay = $configModel->getSetting('social_blog_like_verb_to_display');
        $font = $configModel->getSetting('social_blog_like_font');
        $colorScheme = $configModel->getSetting('social_blog_like_color_scheme');
        $showLinkedIn = $configModel->getSetting('social_blog_like_show_linkedin');
        $showTwitter = $configModel->getSetting('social_blog_like_show_twitter');
        $showGooglePlus = $configModel->getSetting('social_blog_like_show_googleplus');

        $likeText = $this->_getLikeButton($article, $buttonStyle, $showFaces, $showSendButton, $showLinkedIn, $showTwitter, $showGooglePlus, $width, $verbToDisplay, $font, $colorScheme, $renderKeyString, $showLikeInViewPosition, true);
        return $likeText;
    }

    function _getK2ItemLike($article, $configModel, $renderKeyString, $showK2LikeInViewPosition)
    {
        $buttonStyle = $configModel->getSetting('social_k2_item_like_layout_style');
        $showFaces = $configModel->getSetting('social_k2_item_like_show_faces');
        $showSendButton = $configModel->getSetting('social_k2_item_like_show_send_button');
        $width = $configModel->getSetting('social_k2_item_like_width');
        $verbToDisplay = $configModel->getSetting('social_k2_item_like_verb_to_display');
        $font = $configModel->getSetting('social_k2_item_like_font');
        $colorScheme = $configModel->getSetting('social_k2_item_like_color_scheme');
        $showLinkedIn = $configModel->getSetting('social_k2_item_like_show_linkedin');
        $showTwitter = $configModel->getSetting('social_k2_item_like_show_twitter');
        $showGooglePlus = $configModel->getSetting('social_k2_item_like_show_googleplus');

        $likeText = $this->_getLikeButton($article, $buttonStyle, $showFaces, $showSendButton, $showLinkedIn, $showTwitter, $showGooglePlus, $width, $verbToDisplay, $font, $colorScheme, $renderKeyString, $showK2LikeInViewPosition, false);
        return $likeText;
    }

    function _getK2BlogLike($article, $configModel, $renderKeyString, $showK2LikeInViewPosition)
    {
        $buttonStyle = $configModel->getSetting('social_k2_blog_like_layout_style');
        $showFaces = $configModel->getSetting('social_k2_blog_like_show_faces');
        $showSendButton = $configModel->getSetting('social_k2_blog_like_show_send_button');
        $width = $configModel->getSetting('social_k2_blog_like_width');
        $verbToDisplay = $configModel->getSetting('social_k2_blog_like_verb_to_display');
        $font = $configModel->getSetting('social_k2_blog_like_font');
        $colorScheme = $configModel->getSetting('social_k2_blog_like_color_scheme');
        $showLinkedIn = $configModel->getSetting('social_k2_blog_like_show_linkedin');
        $showTwitter = $configModel->getSetting('social_k2_blog_like_show_twitter');
        $showGooglePlus = $configModel->getSetting('social_k2_blog_like_show_googleplus');

        $likeText = $this->_getLikeButton($article, $buttonStyle, $showFaces, $showSendButton, $showLinkedIn, $showTwitter, $showGooglePlus, $width, $verbToDisplay, $font, $colorScheme, $renderKeyString, $showK2LikeInViewPosition, false);
        return $likeText;
    }

    function _getLikeButton($article, $buttonStyle, $showFaces, $showSendButton, $showLinkedInButton, $showTwitterButton, $showGooglePlusButton, $width, $verbToDisplay, $font, $colorScheme, $renderKeyString, $showLikeInViewPosition, $isJoomla)
    {
        $url = SCArticleContent::getCurrentURL($article, $isJoomla);

        //Only set width for standard layout, not box_count or button_count
        if($buttonStyle == 'standard')
            $widthField = ' width=' . $width;
        else
            $widthField = '';

        $likeText = '{JFBCLike layout=' . $buttonStyle . ' show_faces=' . $showFaces . ' show_send_button=' . $showSendButton
                    . $widthField . ' action=' . $verbToDisplay . ' font=' . $font
                    . ' colorscheme=' . $colorScheme . ' href=' . $url . $renderKeyString . '}';

        $buttonText = '<div style="position: relative; top:0px; left:0px; z-index: 98;" class="scsocialbuttons">';
        if ($showLinkedInButton || $showTwitterButton || $showGooglePlusButton)
        {
            $extraButtonText = SCSocialUtilities::getExtraShareButtons($url, $buttonStyle, false, false, $showTwitterButton, $showGooglePlusButton, $renderKeyString, $showLinkedInButton);
            $buttonText .= $extraButtonText;

        }
        $buttonText .= $likeText;
        $buttonText .= '</div><div style="clear:left"></div>';
        $likeText = $buttonText;

        return $likeText;
    }

    function _getJoomlaArticleComments($article, $configModel, $renderKeyString)
    {
        $width = $configModel->getSetting('social_article_comment_width');
        $numposts = $configModel->getSetting('social_article_comment_max_num');
        $colorscheme = $configModel->getSetting('social_article_comment_color_scheme');

        $commentText = $this->_getComments($article, $width, $numposts, $colorscheme, $renderKeyString, true);
        return $commentText;
    }

    function _getJoomlaBlogComments($article, $configModel, $renderKeyString)
    {
        $width = $configModel->getSetting('social_blog_comment_width');
        $numposts = $configModel->getSetting('social_blog_comment_max_num');
        $colorscheme = $configModel->getSetting('social_blog_comment_color_scheme');

        $commentText = $this->_getComments($article, $width, $numposts, $colorscheme, $renderKeyString, true);
        return $commentText;
    }

    function _getK2ItemComments($article, $configModel, $renderKeyString)
    {
        $width = $configModel->getSetting('social_k2_item_comment_width');
        $numposts = $configModel->getSetting('social_k2_item_comment_max_num');
        $colorscheme = $configModel->getSetting('social_k2_item_comment_color_scheme');

        $commentText = $this->_getComments($article, $width, $numposts, $colorscheme, $renderKeyString, false);
        return $commentText;
    }

    function _getK2BlogComments($article, $configModel, $renderKeyString)
    {
        $width = $configModel->getSetting('social_k2_blog_comment_width');
        $numposts = $configModel->getSetting('social_k2_blog_comment_max_num');
        $colorscheme = $configModel->getSetting('social_k2_blog_comment_color_scheme');

        $commentText = $this->_getComments($article, $width, $numposts, $colorscheme, $renderKeyString, false);
        return $commentText;
    }

    function _getComments($article, $width, $numposts, $colorscheme, $renderKeyString, $isJoomla)
    {
        $href = SCArticleContent::getCurrentURL($article, $isJoomla);

        if(!$numposts || $numposts == '0')
        {
            $commentText = '{JFBCCommentsCount href=' . $href . $renderKeyString .'}';
        }
        else
        {
            $commentText = '{JFBCComments href=' . $href . ' width=' . $width . ' num_posts=' . $numposts
                       . ' colorscheme=' . $colorscheme . $renderKeyString . '}';
        }

        return $commentText;
    }
}
