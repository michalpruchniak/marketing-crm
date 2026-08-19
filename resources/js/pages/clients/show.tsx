import { Form, Head, router } from '@inertiajs/react';
import { Eye, KeyRound, Plus, Trash2 } from 'lucide-react';
import { useState } from 'react';
import ClientController from '@/actions/App/Http/Controllers/ClientController';
import ClientCredentialController from '@/actions/App/Http/Controllers/ClientCredentialController';
import Heading from '@/components/heading';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import AddCredentialModal from './__partials/AddCredentialModal';
import RevealCredentialModal from './__partials/RevealCredentialModal';
import { index as clientsIndex } from '@/routes/clients';

type Client = {
    id: string;
    name: string;
    email: string | null;
    phone: string | null;
    notes: string | null;
};

type CredentialMeta = {
    id: string;
    name: string;
    description: string | null;
    type: string;
};

type RevealedCredential = {
    id: string;
    name: string;
    description: string | null;
    login: string;
    password: string;
    additional_information: string | null;
    url?: string | null;
};

export default function ClientsShow({
    client,
    credentials,
}: {
    client: Client;
    credentials: CredentialMeta[];
}) {
    const [createOpen, setCreateOpen] = useState(false);
    const [revealed, setRevealed] = useState<RevealedCredential | null>(null);
    const [revealOpen, setRevealOpen] = useState(false);
    const [revealLoading, setRevealLoading] = useState(false);
    const [revealError, setRevealError] = useState<string | null>(null);

    async function handleReveal(credentialId: string) {
        setRevealLoading(true);
        setRevealError(null);

        try {
            const url = ClientCredentialController.reveal.url({
                client: client.id,
                credential: credentialId,
            });

            const response = await fetch(url, {
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
            });

            const data = (await response.json()) as
                | RevealedCredential
                | { message?: string };

            if (!response.ok) {
                setRevealError(
                    'message' in data && data.message
                        ? data.message
                        : 'Nie można wyświetlić sekretu.',
                );
                return;
            }

            setRevealed(data as RevealedCredential);
            setRevealOpen(true);
        } catch {
            setRevealError(
                'Nie można wyświetlić sekretu. Wystąpił błąd połączenia.',
            );
        } finally {
            setRevealLoading(false);
        }
    }

    function handleDeleteCredential(credentialId: string) {
        if (!confirm('Usunąć ten sekret?')) {
            return;
        }

        router.delete(
            ClientCredentialController.destroy.url({
                client: client.id,
                credential: credentialId,
            }),
            { preserveScroll: true },
        );
    }

    function handleDeleteClient() {
        if (
            !confirm(
                `Usunąć klienta "${client.name}" i powiązane sekrety?`,
            )
        ) {
            return;
        }

        router.delete(ClientController.destroy.url(client.id));
    }

    return (
        <>
            <Head title={client.name} />

            <div className="flex h-full flex-1 flex-col gap-8 p-4">
                <div className="flex flex-wrap items-start justify-between gap-4">
                    <Heading
                        title={client.name}
                        description="Szczegóły klienta oraz sekrety z lokalnej listy metadata"
                    />
                    <Button variant="destructive" onClick={handleDeleteClient}>
                        <Trash2 className="size-4" />
                        Usuń klienta
                    </Button>
                </div>

                <section className="grid max-w-3xl gap-3 rounded-xl border p-4 text-sm">
                    <div>
                        <span className="text-muted-foreground">Email: </span>
                        {client.email ?? '—'}
                    </div>
                    <div>
                        <span className="text-muted-foreground">Phone: </span>
                        {client.phone ?? '—'}
                    </div>
                    <div>
                        <span className="text-muted-foreground">Notes: </span>
                        {client.notes ?? '—'}
                    </div>
                </section>

                <section className="space-y-4">
                    <div className="flex flex-wrap items-start justify-between gap-4">
                        <Heading
                            variant="small"
                            title="Credentials"
                            description={`Lista z lokalnej bazy. Wartości wrażliwe odszyfrowywane dopiero po kliknięciu Show.`}
                        />
                        <Button type="button" onClick={() => setCreateOpen(true)}>
                            <Plus className="size-4" />
                            Dodaj credential
                        </Button>
                    </div>

                    {revealError && (
                        <Alert variant="destructive">
                            <AlertTitle>Nie można wyświetlić sekretu</AlertTitle>
                            <AlertDescription>{revealError}</AlertDescription>
                        </Alert>
                    )}

                    {credentials.length === 0 ? (
                        <p className="text-sm text-muted-foreground">
                            Brak sekretów dla tego
                            klienta.
                        </p>
                    ) : (
                        <div className="overflow-hidden rounded-xl border">
                            <table className="w-full text-left text-sm">
                                <thead className="border-b bg-muted/40">
                                    <tr>
                                        <th className="px-4 py-3 font-medium">
                                            Nazwa
                                        </th>
                                        <th className="px-4 py-3 font-medium">
                                            Opis
                                        </th>
                                        <th className="px-4 py-3 font-medium">
                                            Typ
                                        </th>
                                        <th className="px-4 py-3 font-medium" />
                                    </tr>
                                </thead>
                                <tbody>
                                    {credentials.map((credential) => (
                                        <tr
                                            key={credential.id}
                                            className="border-b last:border-0"
                                        >
                                            <td className="px-4 py-3 font-medium">
                                                <span className="inline-flex items-center gap-2">
                                                    <KeyRound className="size-4 text-muted-foreground" />
                                                    {credential.name}
                                                </span>
                                            </td>
                                            <td className="px-4 py-3 text-muted-foreground">
                                                {credential.description ?? '—'}
                                            </td>
                                            <td className="px-4 py-3 text-muted-foreground">
                                                {credential.type}
                                            </td>
                                            <td className="px-4 py-3">
                                                <div className="flex justify-end gap-2">
                                                    <Button
                                                        type="button"
                                                        variant="outline"
                                                        size="sm"
                                                        disabled={revealLoading}
                                                        onClick={() =>
                                                            handleReveal(
                                                                credential.id,
                                                            )
                                                        }
                                                    >
                                                        <Eye className="size-4" />
                                                        Show
                                                    </Button>
                                                    <Button
                                                        type="button"
                                                        variant="ghost"
                                                        size="sm"
                                                        onClick={() =>
                                                            handleDeleteCredential(
                                                                credential.id,
                                                            )
                                                        }
                                                    >
                                                        <Trash2 className="size-4" />
                                                    </Button>
                                                </div>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    )}
                </section>
            </div>

            <AddCredentialModal
                clientId={client.id}
                open={createOpen}
                onOpenChange={setCreateOpen}
            />

            <RevealCredentialModal
                open={revealOpen}
                onOpenChange={(open) => {
                    setRevealOpen(open);
                    if (!open) {
                        setRevealed(null);
                    }
                }}
                credential={revealed}
            />
        </>
    );
}

ClientsShow.layout = {
    breadcrumbs: [
        {
            title: 'Clients',
            href: clientsIndex(),
        },
        {
            title: 'Client',
            href: '#',
        },
    ],
};
