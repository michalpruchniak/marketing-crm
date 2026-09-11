import { Head, Link, router, usePage } from '@inertiajs/react';
import { Pencil, Plus, Target } from 'lucide-react';
import { useTranslation } from 'react-i18next';
import LeadController from '@/actions/App/Http/Controllers/LeadController';
import Heading from '@/components/heading';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Label } from '@/components/ui/label';
import { useRealtime } from '@/hooks/use-realtime';
import { create as leadsCreate, index as leadsIndex } from '@/routes/leads';
import { leadLabelText } from './label-text';
import type { LeadLabelOption, LeadListItem } from './types';

type Filters = {
    mine: boolean;
};

export default function LeadsIndex({
    leads: initialLeads,
    filters,
}: {
    leads: LeadListItem[];
    filters: Filters;
}) {
    const { t } = useTranslation();
    const { auth, can, leadLabels = [] } = usePage().props;
    const currentUserId = auth.user?.id ?? null;
    const leads = useRealtime(initialLeads, {
        created: {
            channel: 'leads',
            event: '.lead.created',
            resourceKey: 'lead',
        },
        updated: {
            channel: 'leads',
            event: '.lead.updated',
            resourceKey: 'lead',
        },
        shouldInclude: (lead) =>
            !filters.mine || lead.sales_id === currentUserId,
        dependencies: [filters.mine, currentUserId],
    });

    function canUpdateLead(lead: LeadListItem): boolean {
        if (can.canLeadsUpdateAny) {
            return true;
        }

        return currentUserId !== null && lead.sales_id === currentUserId;
    }

    function updateLabel(lead: LeadListItem, label: string) {
        if (label === lead.label) {
            return;
        }

        router.patch(
            LeadController.updateLabel.url(lead.id),
            { label },
            { preserveScroll: true },
        );
    }

    function toggleMineFilter(checked: boolean) {
        router.get(
            leadsIndex.url({ query: { mine: checked ? 1 : 0 } }),
            {},
            {
                preserveState: true,
                preserveScroll: true,
                replace: true,
            },
        );
    }

    return (
        <>
            <Head title={t('leads.pageTitle')} />

            <div className="flex h-full flex-1 flex-col gap-6 p-4">
                <div className="flex items-start justify-between gap-4">
                    <Heading
                        title={t('leads.pageTitle')}
                        description={t('leads.pageDescription')}
                    />

                    {can.canLeadsCreate && (
                        <Button asChild>
                            <Link href={leadsCreate()}>
                                <Plus className="size-4" />
                                {t('leads.addLead')}
                            </Link>
                        </Button>
                    )}
                </div>

                <div className="flex items-center gap-3">
                    <Checkbox
                        id="leads-mine-filter"
                        checked={filters.mine}
                        disabled={!can.canLeadsUpdateAny}
                        onCheckedChange={(checked) =>
                            toggleMineFilter(checked === true)
                        }
                    />
                    <Label htmlFor="leads-mine-filter">
                        {t('leads.onlyMyLeads')}
                    </Label>
                </div>

                {leads.length === 0 ? (
                    <Card>
                        <CardHeader>
                            <CardTitle className="flex items-center gap-2">
                                <Target className="size-5" />
                                {t('leads.noLeadsTitle')}
                            </CardTitle>
                            <CardDescription>
                                {t('leads.noLeadsDescription')}
                            </CardDescription>
                        </CardHeader>
                        {can.canLeadsCreate && (
                            <CardContent>
                                <Button asChild>
                                    <Link href={leadsCreate()}>
                                        {t('leads.addLead')}
                                    </Link>
                                </Button>
                            </CardContent>
                        )}
                    </Card>
                ) : (
                    <div className="overflow-hidden rounded-xl border">
                        <table className="w-full text-left text-sm">
                            <thead className="border-b bg-muted/40">
                                <tr>
                                    <th className="px-4 py-3 font-medium">
                                        {t('common.name')}
                                    </th>
                                    <th className="px-4 py-3 font-medium">
                                        {t('common.email')}
                                    </th>
                                    <th className="px-4 py-3 font-medium">
                                        {t('common.phone')}
                                    </th>
                                    <th className="px-4 py-3 font-medium">
                                        {t('leads.label')}
                                    </th>
                                    {can.canLeadsUpdateAny && !filters.mine && (
                                        <th className="px-4 py-3 font-medium">
                                            {t('leads.salesPerson')}
                                        </th>
                                    )}
                                    <th className="px-4 py-3 font-medium" />
                                </tr>
                            </thead>
                            <tbody>
                                {leads.map((lead) => (
                                    <tr
                                        key={lead.id}
                                        className="border-b last:border-0"
                                    >
                                        <td className="px-4 py-3 font-medium">
                                            {lead.name}
                                        </td>
                                        <td className="px-4 py-3 text-muted-foreground">
                                            {lead.email ?? '—'}
                                        </td>
                                        <td className="px-4 py-3 text-muted-foreground">
                                            {lead.phone ?? '—'}
                                        </td>
                                        <td className="px-4 py-3">
                                            {canUpdateLead(lead) ? (
                                                <select
                                                    value={lead.label}
                                                    onChange={(event) =>
                                                        updateLabel(
                                                            lead,
                                                            event.target.value,
                                                        )
                                                    }
                                                    className="h-8 rounded-md border border-input bg-transparent px-2 text-sm outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50"
                                                >
                                                    {(
                                                        leadLabels as LeadLabelOption[]
                                                    ).map((option) => (
                                                        <option
                                                            key={option}
                                                            value={option}
                                                        >
                                                            {leadLabelText(
                                                                t,
                                                                option,
                                                            )}
                                                        </option>
                                                    ))}
                                                </select>
                                            ) : (
                                                <span className="text-muted-foreground">
                                                    {leadLabelText(
                                                        t,
                                                        lead.label,
                                                    )}
                                                </span>
                                            )}
                                        </td>
                                        {can.canLeadsUpdateAny &&
                                            !filters.mine && (
                                                <td className="px-4 py-3 text-muted-foreground">
                                                    {lead.sales_person?.name ??
                                                        '—'}
                                                </td>
                                            )}
                                        <td className="px-4 py-3">
                                            <div className="flex justify-end">
                                                {canUpdateLead(lead) && (
                                                    <Button
                                                        asChild
                                                        variant="outline"
                                                        size="sm"
                                                    >
                                                        <Link
                                                            href={LeadController.edit.url(
                                                                lead.id,
                                                            )}
                                                        >
                                                            <Pencil className="size-4" />
                                                            {t('common.edit')}
                                                        </Link>
                                                    </Button>
                                                )}
                                            </div>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                )}
            </div>
        </>
    );
}

LeadsIndex.layout = {
    breadcrumbs: [
        {
            title: 'Leads',
            href: leadsIndex(),
        },
    ],
};
