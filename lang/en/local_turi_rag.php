<?php

$string['pluginname'] = 'TURI';

// settings.php
$string['promptsettings'] = 'Prompt service';
$string['promptsettings_desc'] = 'Configuration of the TURI Prompt service.';
$string['prompt_host'] = 'Prompt IP or URL';
$string['prompt_host_desc'] = 'IP address or URL of the TURI Prompt service.';
$string['prompt_port'] = 'Prompt port';
$string['prompt_port_desc'] = 'Port of the TURI Prompt service.';
$string['embeddingsettings'] = 'Embedding service';
$string['embeddingsettings_desc'] = 'Configuration of the TURI Embedding service.';
$string['embedding_host'] = 'Embedding IP or URL';
$string['embedding_host_desc'] = 'IP address or URL of the TURI Embedding service.';
$string['embedding_port'] = 'Embedding port';
$string['embedding_port_desc'] = 'Port of the TURI Embedding service.';
$string['savechanges'] = 'Save changes';


// Navigation.
$string['ragprofile'] = 'TURI RAG Profile';
$string['course'] = 'Course';
$string['explanation'] = 'Explanation';
$string['prompt'] = 'Prompt';

// profile.mustache
$string['rag_profile_title'] = 'RAG Profile';
$string['rag_profile_description'] =
    'This RAG profile contains the documents that are used as the knowledge base for the AI tutor in this course.';

$string['remove_documents'] = 'Remove documents';
$string['edit_rag_profile'] = 'Edit RAG Profile';
$string['delete_rag_profile'] = 'Delete RAG Profile';

$string['documents'] = 'Documents';
$string['document_id'] = 'Document ID';
$string['document_name'] = 'Document name';
$string['file_name'] = 'File name';

// ptofile_edit
$string['edit_rag_profile_description'] = 'Documents marked with a checkmark have already been added to the RAG profile. If you want to add more documents, select them.';
$string['save'] = 'Save';

// profile.create.mustache
$string['select_documents'] = 'Select documents';
$string['select_documents_description'] = 'Select the documents you want to include in your RAG profile.';
$string['cancel'] = 'Cancel';
$string['continue'] = 'Continue';

// profile_remove_documents
$string['remove_documents'] = 'Remove documents';
$string['remove_documents_description'] = 'Select the documents you want to remove from the RAG profile.';
$string['delete'] = 'Delete';
$string['documents_removed_success'] = 'Selected documents have been successfully removed.';
$string['profile_delete_accepted'] = 'The deletion request has been accepted. The profile will be removed shortly.';

// explanation_empty.mustache
$string['no_rag_profile'] = 'No RAG profile has been created for this course yet.';
$string['no_rag_profile_description'] =
    'A RAG profile defines which course documents the AI uses as its knowledge base. Create a profile by selecting the desired documents.';
$string['rag_profile_step_by_step'] = 'After clicking Start now, you will be guided through the setup step by step.';
$string['more_information'] = 'For more information about the RAG profile and its configuration, please visit the Explanation page.';
$string['start'] = 'Start now';

// explanation_info.mustache
$string['what_is_rag_profile'] = 'What is a RAG profile?';
$string['what_is_rag_profile_desc'] = 'A RAG profile defines how the AI assistant uses course materials to retrieve relevant information and generate answers based on the selected documents and system prompt.';
$string['create_rag_profile'] = 'Create RAG Profile';
$string['create_rag_profile_desc'] = 'A RAG profile is created automatically once you select and upload the desired documents. The selected documents are then used as the knowledge base for answering questions within the RAG profile.';
$string['select_upload_documents'] = 'Select and Upload Documents';
$string['select_upload_documents_desc'] = 'Select the documents that should be used for the RAG profile. Once you have selected and submitted the documents, the RAG profile will be created automatically. The uploaded documents form the knowledge base for the AI responses.';
$string['course_assignment'] = 'Course Assignment';
$string['course_assignment_desc'] = 'A RAG profile always belongs to exactly one course. Therefore, the profile can only be used within the selected course and provides the knowledge base for that course.';
$string['activate_ai_assistant'] = 'Activate the AI Assistant';
$string['activate_ai_assistant_desc'] = 'After creating the RAG profile, you need to activate the AI Assistant block in the course. Only then will learners enrolled in the course be able to use the AI Assistant.';
$string['select_system_prompt'] = 'Select System Prompt';
$string['select_system_prompt_desc'] = 'Select a suitable system prompt to define the role and behavior of the AI. The system prompt determines how the AI should respond to questions and which rules, requirements, or restrictions it must consider when generating its responses. Depending on the selected prompt, the AI may, for example, be more strictly limited to the provided documents or respond with fewer restrictions.';
$string['delete_rag_profile'] = 'Delete RAG Profile';
$string['delete_rag_profile_desc'] = 'When a RAG profile is deleted, all documents associated with the profile are also deleted. In addition, the usage history of the RAG profile is removed. This includes previous questions and interactions of users who have used the RAG profile.';
$string['note'] = 'Note:';
$string['delete_rag_profile_note'] = 'The deletion of a RAG profile should therefore be carefully considered, as the associated documents and usage history will not be retained.';


// profile_creation_result.mustache
$string['ragprofilecreation'] = 'Create RAG profile';
$string['ragprofilesuccess'] = 'You have successfully created the RAG profile.';
$string['ragprofilecreating'] = 'The RAG profile is being created.';
$string['ragprofileinactive'] = 'No RAG profile has been created for this course yet.';

// wizard_progress.mustache
$string['step_start'] = 'Start';
$string['documents_selected'] = 'Documents selected';
$string['rag_profile_creation'] = 'RAG profile creation';
$string['system_prompt'] = 'System prompt';
$string['completed'] = 'Completed';

// mod
$string['delete_profile_confirm_title'] = 'Delete RAG profile';
$string['delete_profile_confirm_body'] = 'After deleting this profile, learners will no longer be able to use the AI assistant. Are you sure you want to delete it?';

// error classes/local/turi_client.php
$string['embedding_config_missing'] = 'The TURI service is not configured correctly. Please contact the administrator.';
$string['turi_connection_error'] = 'The TURI service is currently unavailable. Please try again later.';
$string['turi_http_error'] = 'The TURI service returned an error. Please try again later.';
$string['turi_invalid_response'] = 'The TURI service returned an unexpected response. Please contact the administrator.';

$string['profile_delete_accepted'] = 'The deletion request has been accepted. The profile will be removed shortly.';
$string['error_deleting_profile'] = 'An error occurred while attempting to delete the RAG profile. Please try again later.';

// profile_notification.js
$string['rag_status_success'] = 'RAG profile creation completed successfully! Redirecting...';
$string['rag_status_inprogress'] = 'RAG profile creation in progress, please wait...';
$string['rag_status_error'] = 'An error occurred during processing or an unexpected status was received.';