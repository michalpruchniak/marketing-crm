import { Form } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import InputError from '@/components/input-error';
import PasswordInput from '@/components/password-input';
import ClientCredentialController from '@/actions/App/Http/Controllers/ClientCredentialController';

type Props = {
    clientId: string;
    open: boolean;
    onOpenChange: (open: boolean) => void;
};

export default function AddCredentialModal({
    clientId,
    open,
    onOpenChange,
}: Props) {
    return (
        <Dialog open={open} onOpenChange={onOpenChange}>
            <DialogContent className="sm:max-w-lg">
                <DialogHeader>
                    <DialogTitle>Dodaj credential</DialogTitle>
                </DialogHeader>

                <Form
                    {...ClientCredentialController.store.form(clientId)}
                    options={{ preserveScroll: true }}
                    resetOnSuccess
                    onSuccess={() => onOpenChange(false)}
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
                                <Label htmlFor="url">Adres URL (opcjonalnie)</Label>
                                <Input id="url" name="url" type="url" placeholder="https://example.com" />
                                <InputError message={errors.url} />
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
                                    onClick={() => onOpenChange(false)}
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
    );
}
