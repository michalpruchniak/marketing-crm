import { Form, Head, usePage } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import ClientController from '@/actions/App/Http/Controllers/ClientController';
import Heading from '@/components/heading';
import {
    index as clientsIndex,
    create as clientsCreate,
} from '@/routes/clients';
import CreateClientForm from './Form';

export default function ClientsCreate() {
    const { t } = useTranslation();
    const { can, coordinators = [] } = usePage().props;

    return (
        <>
            <Head title={t('clients.createTitle')} />

            <div className="flex h-full flex-1 flex-col gap-6 p-4">
                <Heading
                    title={t('clients.createTitle')}
                    description={t('clients.createDescription')}
                />

                <Form
                    {...ClientController.store.form()}
                    className="max-w-xl space-y-6"
                >
                    {({ processing, errors }) => (
                        <CreateClientForm
                            processing={processing}
                            errors={errors}
                            canAssignCoordinator={
                                can.canClientsAssignCoordinator
                            }
                            coordinators={coordinators}
                        />
                    )}
                </Form>
            </div>
        </>
    );
}

ClientsCreate.layout = {
    breadcrumbs: [
        {
            title: 'Clients',
            href: clientsIndex(),
        },
        {
            title: 'Add client',
            href: clientsCreate(),
        },
    ],
};
