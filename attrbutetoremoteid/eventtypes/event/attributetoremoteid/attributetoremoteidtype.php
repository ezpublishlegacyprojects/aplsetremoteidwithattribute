<?php
//
// Definition of attributetoremoteidType class
//
// Copyright (C) Aplyca Tecnologia <ls@ez.no>.
//
// This file may be distributed and/or modified under the terms of the
// 'GNU General Public License' version 2 as published by the Free
// Software Foundation and appearing in the file LICENSE included in
// the packaging of this file.
//
// This file is provided AS IS with NO WARRANTY OF ANY KIND, INCLUDING
// THE WARRANTY OF DESIGN, MERCHANTABILITY AND FITNESS FOR A PARTICULAR
// PURPOSE.
//
// The 'GNU General Public License' (GPL) is available at
// http://www.gnu.org/copyleft/gpl.html.
//


include_once 'lib/ezxml/classes/ezxml.php';
include_once 'lib/ezutils/classes/ezoperationhandler.php';
include_once 'kernel/classes/ezcontentobject.php';

include_once( 'kernel/classes/ezworkflowtype.php' );
include_once( 'lib/ezutils/classes/ezlog.php' );
include_once( "kernel/classes/ezaudit.php" );
include_once('lib/ezutils/classes/ezsys.php');
include_once( 'lib/ezutils/classes/ezini.php' );
include_once( 'kernel/common/template.php' );

define( 'EZ_WORKFLOW_TYPE_ATTRIBUTETOREMOTEID', 'attributetoremoteid' );

class attributetoremoteidType extends eZWorkflowEventType
{

    function attributetoremoteidType()
    {
        $this->eZWorkflowEventType( EZ_WORKFLOW_TYPE_ATTRIBUTETOREMOTEID, ezi18n( 'kernel/workflow/event', 'AttributetoremoteID' ) );
        $this->setTriggerTypes( array( 'content' => array( 'publish' => array ( 'after' ) ) ) );
    }

    function execute( &$process, &$event )
    {
        $parameters = $process->attribute( 'parameter_list' );
		$versionID =& $parameters['version'];
        $object =& eZContentObject::fetch( $parameters['object_id'] );
		$node =& $object->attribute( 'main_node' );

		// Obtain AttributeToRemoteIDSettings 
		$ini =& eZINI::instance( 'attributetoremoteid.ini');

	    // Get Class Id
	    $content_classID =& $object->attribute( 'contentclass_id' );
    
	    // Get attribute identifier 
	    $attribute_identifier = $ini->variable( "AttributeToRemoteIDSettings_$content_classID", 'ContentClassAttributeIdentifier' );
		
		//Get datamap of the published object
		$dataMap =& $node->attribute( 'data_map' );

		/////////////////////////////
		// Begin Replacing remote_id
		////////////////////////////

		if ($attribute_identifier){
			$attribute_to_remoteID = $dataMap[$attribute_identifier];
			if($attribute_to_remoteID){
				$attribute_datatext =& $attribute_to_remoteID->attribute( 'data_text' );
				if($attribute_datatext){
					$object->setAttribute( 'remote_id', $attribute_datatext );
					$object->store();
				}
			}
		}


        return EZ_WORKFLOW_TYPE_STATUS_ACCEPTED;
    }
}

eZWorkflowEventType::registerType( EZ_WORKFLOW_TYPE_ATTRIBUTETOREMOTEID, 'attributetoremoteidtype' );

?>
