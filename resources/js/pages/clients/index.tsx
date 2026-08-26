import { Head, Link } from '@inertiajs/react';
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
import {
    index as clientsIndex,
    create as clientsCreate,
    show as clientsShow,
} from '@/routes/clients';

type ClientListItem = {
    id: string;
    name: string;
    email: string | null;
    phone: string | null;
    created_at: string | null;
};

export default function ClientsIndex({
    clients,
}: {
    clients: ClientListItem[];
}) {
    const { t } = useTranslation();

    return (
        <>
            <Head title={t('clients.pageTitle')} />

            <div className="flex h-full flex-1 flex-col gap-6 p-4">
                <div className="flex items-start justify-between gap-4">
                    <Heading
                        title={t('clients.pageTitle')}
                        description={t('clients.pageDescription')}
                    />

                    <Button asChild>
                        <Link href={clientsCreate()}>
                            <Plus className="size-4" />
                            {t('clients.addClient')}
                        </Link>
                    </Button>
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
                        <CardContent>
                            <Button asChild>
                                <Link href={clientsCreate()}>
                                    {t('clients.addClient')}
                                </Link>
                            </Button>
                        </CardContent>
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
                                    <th className="px-4 py-3 font-medium" />
                                </tr>
                            </thead>
                            <tbody>
                                {clients.map((client) => (
                                    <tr
                                        key={client.id}
                                        className="border-b last:border-0"
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
                                ))}
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
