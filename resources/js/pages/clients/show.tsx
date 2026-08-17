import { Form, Head, router } from '@inertiajs/react';
import { Eye, KeyRound, Plus, Trash2 } from 'lucide-react';
import { useState } from 'react';
import ClientController from '@/actions/App/Http/Controllers/ClientController';
import ClientCredentialController from '@/actions/App/Http/Controllers/ClientCredentialController';
import Heading from '@/components/heading';
import InputError from '@/components/input-error';
import PasswordInput from '@/components/password-input';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
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

            <Dialog open={createOpen} onOpenChange={setCreateOpen}>
                <DialogContent className="sm:max-w-lg">
                    <DialogHeader>
                        <DialogTitle>Dodaj credential</DialogTitle>
                    </DialogHeader>

                    <Form
                        {...ClientCredentialController.store.form(client.id)}
                        options={{ preserveScroll: true }}
                        resetOnSuccess
                        onSuccess={() => setCreateOpen(false)}
                        className="space-y-4"
                    >
                        {({ processing, errors }) => (
                            <>
                                <div className="grid gap-2">
                                    <Label htmlFor="name">Nazwa</Label>
                                    <Input
                                        id="name"
                                        name="name"
                                        required
                                        autoFocus
                                        placeholder="FTP / Panel / Email"
                                    />
                                    <InputError message={errors.name} />
                                </div>

                                <div className="grid gap-2">
                                    <Label htmlFor="description">Opis</Label>
                                    <Textarea
                                        id="description"
                                        name="description"
                                        placeholder="Opcjonalny opis"
                                    />
                                    <InputError message={errors.description} />
                                </div>

                                <div className="grid gap-2">
                                    <Label htmlFor="login">Login</Label>
                                    <Input
                                        id="login"
                                        name="login"
                                        required
                                        autoComplete="off"
                                    />
                                    <InputError message={errors.login} />
                                </div>

                                <div className="grid gap-2">
                                    <Label htmlFor="password">Hasło</Label>
                                    <PasswordInput
                                        id="password"
                                        name="password"
                                        required
                                        autoComplete="new-password"
                                    />
                                    <InputError message={errors.password} />
                                </div>

                                <div className="grid gap-2">
                                    <Label htmlFor="additional_information">
                                        Dodatkowe informacje
                                    </Label>
                                    <Textarea
                                        id="additional_information"
                                        name="additional_information"
                                        placeholder="PIN, kody recovery, itd."
                                    />
                                    <InputError
                                        message={errors.additional_information}
                                    />
                                </div>

                                <DialogFooter>
                                    <Button
                                        type="button"
                                        variant="outline"
                                        onClick={() => setCreateOpen(false)}
                                    >
                                        Anuluj
                                    </Button>
                                    <Button type="submit" disabled={processing}>
                                        Zapisz
                                    </Button>
                                </DialogFooter>
                            </>
                        )}
                    </Form>
                </DialogContent>
            </Dialog>

            <Dialog
                open={revealOpen}
                onOpenChange={(open) => {
                    setRevealOpen(open);
                    if (!open) {
                        setRevealed(null);
                    }
                }}
            >
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>
                            {revealed?.name ?? 'Credential'}
                        </DialogTitle>
                        <DialogDescription>
                            {revealed?.description ??
                                'Odszyfrowane dane tego wpisu.'}
                        </DialogDescription>
                    </DialogHeader>

                    {revealed && (
                        <div className="space-y-3 text-sm">
                            <div>
                                <div className="text-muted-foreground">
                                    Login
                                </div>
                                <div className="font-mono">{revealed.login}</div>
                            </div>
                            <div>
                                <div className="text-muted-foreground">
                                    Hasło
                                </div>
                                <div className="font-mono">
                                    {revealed.password}
                                </div>
                            </div>
                            {revealed.additional_information && (
                                <div>
                                    <div className="text-muted-foreground">
                                        Dodatkowe informacje
                                    </div>
                                    <div className="whitespace-pre-wrap font-mono">
                                        {revealed.additional_information}
                                    </div>
                                </div>
                            )}
                        </div>
                    )}
                </DialogContent>
            </Dialog>
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
