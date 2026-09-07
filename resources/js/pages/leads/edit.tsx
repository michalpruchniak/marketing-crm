import { Form, Head, usePage } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import LeadController from '@/actions/App/Http/Controllers/LeadController';
import Heading from '@/components/heading';
import { index as leadsIndex } from '@/routes/leads';
import LeadForm from './Form';
import type { Lead } from './types';

export default function LeadsEdit({ lead }: { lead: Lead }) {
    const { t } = useTranslation();
    const { can, leadLabels = [], salesPersons = [] } = usePage().props;

    return (
        <>
            <Head title={t('leads.editTitle')} />

            <div className="flex h-full flex-1 flex-col gap-6 p-4">
                <Heading
                    title={t('leads.editTitle')}
                    description={t('leads.editDescription')}
                />

                <Form
                    {...LeadController.update.form(lead.id)}
                    className="max-w-xl space-y-6"
                >
                    {({ processing, errors }) => (
                        <LeadForm
                            processing={processing}
                            errors={errors}
                            leadLabels={leadLabels}
                            salesPersons={salesPersons}
                            defaults={lead}
                            showSalesPerson={can.canLeadsUpdateAny}
                        />
                    )}
                </Form>
            </div>
        </>
    );
}

LeadsEdit.layout = {
    breadcrumbs: [
        {
            title: 'Leads',
            href: leadsIndex(),
        },
        {
            title: 'Edit lead',
            href: '#',
        },
    ],
};
