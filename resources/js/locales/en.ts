const en = {
    common: {
        // Field labels
        name: 'Name',
        email: 'Email',
        phone: 'Phone',
        notes: 'Notes',
        description: 'Description',
        login: 'Login',
        password: 'Password',
        url: 'URL',
        additionalInformation: 'Additional information',

        // Actions
        save: 'Save',
        cancel: 'Cancel',
        delete: 'Delete',
        edit: 'Edit',
        open: 'Open',
        add: 'Add',
        show: 'Show',
    },

    clients: {
        // Index
        pageTitle: 'Clients',
        pageDescription: 'Manage clients and their stored credentials.',
        addClient: 'Add client',
        noClientsTitle: 'No clients yet',
        noClientsDescription:
            'Create your first client to start storing credentials.',
        tableHeadPhone: 'Phone',
        coordinator: 'Coordinator',

        // Create
        createTitle: 'Add client',
        createDescription:
            'Create a client profile. You can attach credentials afterwards.',
        namePlaceholder: 'Acme Sp. z o.o.',
        emailPlaceholder: 'contact@example.com',
        phonePlaceholder: '+48 123 456 789',
        notesPlaceholder: 'Notes about this client',
        saveClient: 'Save client',

        // Show / Edit
        clientDetailsDescription:
            'Client details and credentials from the local metadata list.',
        editClientTitle: 'Edit client',
        deleteClient: 'Delete client',
        deleteClientConfirmTitle: 'Delete client',
        deleteClientConfirmDescription:
            'Are you sure you want to delete "{{name}}" and all associated credentials? This action cannot be undone.',

        // Credentials section
        credentialsTitle: 'Credentials',
        credentialsDescription:
            'Listed from the local database. Sensitive values are decrypted only after clicking Show.',
        addCredential: 'Add credential',
        noCredentials: 'No credentials for this client.',
        credTableHeadType: 'Type',
        deleteCredentialConfirmTitle: 'Delete credential',
        deleteCredentialConfirmDescription:
            'Are you sure you want to delete the credential "{{name}}"? This action cannot be undone.',
        revealErrorTitle: 'Cannot reveal secret',
        revealErrorConnection:
            'Cannot reveal secret. A connection error occurred.',

        // AddCredentialModal
        addCredentialTitle: 'Add credential',
        credNamePlaceholder: 'FTP / Panel / Email',
        descriptionPlaceholder: 'Description',
        urlPlaceholder: 'https://example.com',
        additionalInformationPlaceholder: 'PIN, recovery codes, etc.',
        encryptedFieldsTitle: 'Encrypted fields',
        encryptedFieldsDescription:
            'These values are encrypted before they are stored.',

        // RevealCredentialModal
        revealModalTitle: 'Credential',
    },
} as const;

export default en;
export type Translation = typeof en;
