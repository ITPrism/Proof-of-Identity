<?php
/**
 * @package      Identityproof
 * @subpackage   Service
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * This controller provides functionality
 * to verify users via third-party services.
 *
 * @package        Identityproof
 * @subpackage     Service
 */
class IdentityproofControllerService extends JControllerLegacy
{
    /**
     * Method to get a model object, loading it if required.
     *
     * @param    string $name   The model name. Optional.
     * @param    string $prefix The class prefix. Optional.
     * @param    array  $config Configuration array for model. Optional.
     *
     * @return    IdentityproofModelService    The model.
     * @since    1.5
     */
    public function getModel($name = 'Service', $prefix = 'IdentityproofModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }

    /**
     * Trigger plugin event that verifies a user by third-party service.
     *
     * @throws Exception
     * @throws UnexpectedValueException
     * @throws RuntimeException
     */
    public function verify()
    {
        // Get verification service.
        $service = JString::strtolower($this->input->getCmd('service'));
        if (!$service) {
            throw new UnexpectedValueException(JText::_('COM_IDENTITYPROOF_ERROR_INVALID_SERVICE'));
        }

        // Get component parameters
        $params = JComponentHelper::getParams('com_identityproof');
        /** @var  $params Joomla\Registry\Registry */
        
        $output = array();

        // Trigger the event
        try {
            
            $context = 'com_identityproof.service.'.$service;
            
            // Import component plugin.
            $dispatcher = JEventDispatcher::getInstance();
            JPluginHelper::importPlugin('identityproof');

            // Trigger the event.
            $results = $dispatcher->trigger('onVerify', array($context, &$params));

            // Get the result, that comes from the plugin.
            if (is_array($results) and count($results) > 0) {
                foreach ($results as $result) {
                    if ($result !== null and is_array($result)) {
                        $output = $result;
                        break;
                    }
                }
            }

        } catch (UnexpectedValueException $e) {

            $this->setMessage($e->getMessage(), 'notice');
            $this->setRedirect(JRoute::_(IdentityproofHelperRoute::getProofRoute(), false));
            return;

        } catch (RuntimeException $e) {

            $this->setMessage($e->getMessage(), 'warning');
            $this->setRedirect(JRoute::_(IdentityproofHelperRoute::getProofRoute(), false));
            return;

        } catch (Exception $e) {

            JLog::add($e->getMessage(), JLog::ERROR, 'com_identityproof');
            throw new Exception(JText::_('COM_IDENTITYPROOF_ERROR_SYSTEM'));
        }

        $redirectUrl = Joomla\Utilities\ArrayHelper::getValue($output, 'redirect_url');
        $message     = Joomla\Utilities\ArrayHelper::getValue($output, 'message');
        if (!$redirectUrl) {
            throw new UnexpectedValueException(JText::_('COM_IDENTITYPROOF_ERROR_INVALID_REDIRECT_URL'));
        }

        if (!$message) {
            $this->setRedirect($redirectUrl);
        } else {
            $this->setRedirect($redirectUrl, $message, 'warning');
        }
    }

    /**
     * Trigger plugin event that removes a service record.
     *
     * @throws Exception
     */
    public function remove()
    {
        // Check for request forgeries.
        JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));

        // Get verification service.
        $service = JString::strtolower($this->input->getCmd('service'));
        if (!$service) {
            throw new UnexpectedValueException(JText::_('COM_IDENTITYPROOF_ERROR_INVALID_SERVICE'));
        }

        // Get component parameters
        $params = JComponentHelper::getParams('com_identityproof');
        /** @var  $params Joomla\Registry\Registry */

        $output = array();

        // Trigger the event
        try {

            $context = 'com_identityproof.service.'.$service;

            // Import component plugin.
            $dispatcher = JEventDispatcher::getInstance();
            JPluginHelper::importPlugin('identityproof');

            // Trigger the event.
            $results = $dispatcher->trigger('onRemove', array($context, &$params));

            // Get the result, that comes from the plugin.
            if (is_array($results) and count($results) > 0) {
                foreach ($results as $result) {
                    if ($result !== null and is_array($result)) {
                        $output = $result;
                        break;
                    }
                }
            }

        } catch (UnexpectedValueException $e) {
            $this->setMessage($e->getMessage(), 'notice');
            $this->setRedirect(JRoute::_(IdentityproofHelperRoute::getProofRoute(), false));
            return;
        } catch (RuntimeException $e) {
            $this->setMessage($e->getMessage(), 'warning');
            $this->setRedirect(JRoute::_(IdentityproofHelperRoute::getProofRoute(), false));
            return;

        } catch (Exception $e) {
            JLog::add($e->getMessage(), JLog::ERROR, 'com_identityproof');
            throw new Exception(JText::_('COM_IDENTITYPROOF_ERROR_SYSTEM'));
        }

        $redirectUrl = Joomla\Utilities\ArrayHelper::getValue($output, 'redirect_url');
        $message     = Joomla\Utilities\ArrayHelper::getValue($output, 'message');
        if (!$redirectUrl) {
            throw new UnexpectedValueException(JText::_('COM_IDENTITYPROOF_ERROR_INVALID_REDIRECT_URL'));
        }

        if (!$message) {
            $this->setRedirect($redirectUrl);
        } else {
            $this->setRedirect($redirectUrl, $message, 'warning');
        }
    }
}
