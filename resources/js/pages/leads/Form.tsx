import { Link, usePage } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import FieldLabel from '@/pages/clients/components/FieldLabel';
import { index as leadsIndex } from '@/routes/leads';
import { leadLabelText } from './label-text';
import type { DefaultsLeads, LeadLabelOption, SalesPersonOption } from './types';

type FormErrors = Partial<
    Record<'name' | 'email' | 'phone' | 'notes' | 'label' | 'sales_id', string>
>;

type Props = {
    processing: boolean;
    errors: FormErrors;
    leadLabels: LeadLabelOption[];
    salesPersons?: SalesPersonOption[];
    defaults?: DefaultsLeads;
    showSalesPerson?: boolean;
};

export default function LeadForm({
    processing,
    errors,
    // leadLabels,
    defaults = {},
    salesPersons = [],
    showSalesPerson = false,
}: Props) {
    const { t } = useTranslation();
    const { leadLabels = [] } = usePage().props;

    return (
        <>
            <div className="grid gap-2">
                <FieldLabel htmlFor="lead-name" required>
                    {t('common.name')}
                </FieldLabel>
                <Input
                    id="lead-name"
                    name="name"
                    required
                    autoFocus
                    defaultValue={defaults.name ?? ''}
                    placeholder={t('leads.namePlaceholder')}
                />
                <InputError message={errors.name} />
            </div>

            <div className="grid gap-2">
                <FieldLabel htmlFor="lead-email">
                    {t('common.email')}
                </FieldLabel>
                <Input
                    id="lead-email"
                    type="email"
                    name="email"
                    defaultValue={defaults.email ?? ''}
                    placeholder={t('leads.emailPlaceholder')}
                />
                <InputError message={errors.email} />
            </div>

            <div className="grid gap-2">
                <FieldLabel htmlFor="lead-phone">
                    {t('common.phone')}
                </FieldLabel>
                <Input
                    id="lead-phone"
                    name="phone"
                    defaultValue={defaults.phone ?? ''}
                    placeholder={t('leads.phonePlaceholder')}
                />
                <InputError message={errors.phone} />
            </div>

            <div className="grid gap-2">
                <FieldLabel htmlFor="lead-label" required>
                    {t('leads.label')}
                </FieldLabel>
                <select
                    id="lead-label"
                    name="label"
                    required
                    defaultValue={defaults.label ?? 'new'}
                    className="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50"
                >
                    {leadLabels.map((option) => (
                        <option key={option} value={option}>
                            {leadLabelText(t, option)}
                        </option>
                    ))}
                </select>
                <InputError message={errors.label} />
            </div>

            {showSalesPerson && (
                <div className="grid gap-2">
                    <FieldLabel htmlFor="lead-sales_id">
                        {t('leads.salesPerson')}
                    </FieldLabel>
                    <select
                        id="lead-sales_id"
                        name="sales_id"
                        defaultValue={defaults.sales_id ?? ''}
                        className="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50"
                    >
                        <option value="">
                            {t('leads.salesPersonPlaceholder')}
                        </option>
                        {salesPersons.map((person) => (
                            <option key={person.id} value={person.id}>
                                {person.name}
                            </option>
                        ))}
                    </select>
                    <InputError message={errors.sales_id} />
                </div>
            )}

            <div className="grid gap-2">
                <FieldLabel htmlFor="lead-notes">
                    {t('common.notes')}
                </FieldLabel>
                <Textarea
                    id="lead-notes"
                    name="notes"
                    defaultValue={defaults.notes ?? ''}
                    placeholder={t('leads.notesPlaceholder')}
                />
                <InputError message={errors.notes} />
            </div>

            <div className="flex gap-3">
                <Button type="submit" disabled={processing}>
                    {t('common.save')}
                </Button>
                <Button variant="outline" asChild>
                    <Link href={leadsIndex()}>{t('common.cancel')}</Link>
                </Button>
            </div>
        </>
    );
}
