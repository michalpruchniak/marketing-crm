import { Head, Link, usePage } from '@inertiajs/react';
import { Plus, Users } from 'lucide-react';
import { useTranslation } from 'react-i18next';
import Heading from '@/components/heading';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { useRealtime } from '@/hooks/use-realtime';
import { cn } from '@/lib/utils';
import {
    create as clientsCreate,
    index as clientsIndex,
    show as clientsShow,
} from '@/routes/clients';
import type { Client } from './types';

export default function ClientsIndex({
    clients: initialClients,
}: {
    clients: Client[];
}) {
    const { t } = useTranslation();
    const { auth, can } = usePage().props;
    const currentUserId = auth.user?.id ?? null;
    const clients = useRealtime(initialClients, {
        created: {
            channel: 'clients',
            event: '.client.created',
            resourceKey: 'client',
        },
        updated: {
            channel: 'clients',
            event: '.client.updated',
            resourceKey: 'client',
        },
        deleted: {
            channel: 'clients',
            event: '.client.deleted',
            resourceKey: 'client',
        },
    });

    return (
        <>
            <Head title={t('clients.pageTitle')} />

            <div className="flex h-full flex-1 flex-col gap-6 p-4">
                <div className="flex items-start justify-between gap-4">
                    <Heading
                        title={t('clients.pageTitle')}
                        description={t('clients.pageDescription')}
                    />

                    {can.canClientsCreate && (
                        <Button asChild>
                            <Link href={clientsCreate()}>
                                <Plus className="size-4" />
                                {t('clients.addClient')}
                            </Link>
                        </Button>
                    )}
                </div>

                {clients.length === 0 ? (
                    <Card>
                        <CardHeader>
                            <CardTitle className="flex items-center gap-2">
                                <Users className="size-5" />
                                {t('clients.noClientsTitle')}
                            </CardTitle>

                            <CardDescription>
                                {t('clients.noClientsDescription')}
                            </CardDescription>
                        </CardHeader>

                        {can.canClientsCreate && (
                            <CardContent>
                                <Button asChild>
                                    <Link href={clientsCreate()}>
                                        {t('clients.addClient')}
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
                                        {t('clients.coordinator')}
                                    </th>

                                    <th className="px-4 py-3 font-medium" />
                                </tr>
                            </thead>

                            <tbody>
                                {clients.map((client) => {
                                    const isOwn =
                                        currentUserId !== null &&
                                        client.coordinator_id === currentUserId;

                                    return (
                                        <tr
                                            key={client.id}
                                            className={cn(
                                                'border-b last:border-0',
                                                isOwn &&
                                                    'bg-amber-50/80 dark:bg-amber-950/30',
                                            )}
                                        >
                                            <td className="px-4 py-3 font-medium">
                                                {client.name}
                                            </td>

                                            <td className="px-4 py-3 text-muted-foreground">
                                                {client.email ?? '—'}
                                            </td>

                                            <td className="px-4 py-3 text-muted-foreground">
                                                {client.phone ?? '—'}
                                            </td>

                                            <td className="px-4 py-3 text-muted-foreground">
                                                {client.coordinator?.name ??
                                                    '—'}
                                            </td>

                                            <td className="px-4 py-3 text-right">
                                                <Button
                                                    variant="outline"
                                                    size="sm"
                                                    asChild
                                                >
                                                    <Link
                                                        href={clientsShow(
                                                            client.id,
                                                        )}
                                                    >
                                                        {t('common.open')}
                                                    </Link>
                                                </Button>
                                            </td>
                                        </tr>
                                    );
                                })}
                            </tbody>
                        </table>
                    </div>
                )}
            </div>
        </>
    );
}

ClientsIndex.layout = {
    breadcrumbs: [
        {
            title: 'Clients',
            href: clientsIndex(),
        },
    ],
};
