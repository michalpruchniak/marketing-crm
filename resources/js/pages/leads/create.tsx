import { Form, Head, usePage } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import LeadController from '@/actions/App/Http/Controllers/LeadController';
import Heading from '@/components/heading';
import { create as leadsCreate, index as leadsIndex } from '@/routes/leads';
import LeadForm from './Form';

export default function LeadsCreate() {
    const { t } = useTranslation();
    const { can, leadLabels = [], salesPersons = [] } = usePage().props;

    return (
        <>
            <Head title={t('leads.createTitle')} />

            <div className="flex h-full flex-1 flex-col gap-6 p-4">
                <Heading
                    title={t('leads.createTitle')}
                    description={t('leads.createDescription')}
                />

                <Form
                    {...LeadController.store.form()}
                    className="max-w-xl space-y-6"
                >
                    {({ processing, errors }) => (
                        <LeadForm
                            processing={processing}
                            errors={errors}
                            leadLabels={leadLabels}
                            salesPersons={salesPersons}
                            showSalesPerson={can.canLeadsUpdateAny}
                        />
                    )}
                </Form>
            </div>
        </>
    );
}

LeadsCreate.layout = {
    breadcrumbs: [
        {
            title: 'Leads',
            href: leadsIndex(),
        },
        {
            title: 'Add lead',
            href: leadsCreate(),
        },
    ],
};
