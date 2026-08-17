import { Form, Head, Link } from '@inertiajs/react';
import ClientController from '@/actions/App/Http/Controllers/ClientController';
import Heading from '@/components/heading';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { index as clientsIndex, create as clientsCreate } from '@/routes/clients';

export default function ClientsCreate() {
    return (
        <>
            <Head title="Add client" />

            <div className="flex h-full flex-1 flex-col gap-6 p-4">
                <Heading
                    title="Add client"
                    description="Create a client profile. You can attach passwords afterwards."
                />

                <Form
                    {...ClientController.store.form()}
                    className="max-w-xl space-y-6"
                >
                    {({ processing, errors }) => (
                        <>
                            <div className="grid gap-2">
                                <Label htmlFor="name">Name</Label>
                                <Input
                                    id="name"
                                    name="name"
                                    required
                                    autoFocus
                                    placeholder="Acme Sp. z o.o."
                                />
                                <InputError message={errors.name} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="email">Email</Label>
                                <Input
                                    id="email"
                                    type="email"
                                    name="email"
                                    placeholder="kontakt@example.com"
                                />
                                <InputError message={errors.email} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="phone">Phone</Label>
                                <Input
                                    id="phone"
                                    name="phone"
                                    placeholder="+48 123 456 789"
                                />
                                <InputError message={errors.phone} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="notes">Notes</Label>
                                <Textarea
                                    id="notes"
                                    name="notes"
                                    placeholder="Optional notes about this client"
                                />
                                <InputError message={errors.notes} />
                            </div>

                            <div className="flex gap-3">
                                <Button type="submit" disabled={processing}>
                                    Save client
                                </Button>
                                <Button variant="outline" asChild>
                                    <Link href={clientsIndex()}>Cancel</Link>
                                </Button>
                            </div>
                        </>
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
