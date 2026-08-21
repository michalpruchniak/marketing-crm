import { Form } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
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

export default function AddCredentialModal({ clientId, open, onOpenChange }: Props) {
    const { t } = useTranslation('clients');
    const { t: tc } = useTranslation('common');

    return (
        <Dialog open={open} onOpenChange={onOpenChange}>
            <DialogContent className="sm:max-w-lg">
                <DialogHeader>
                    <DialogTitle>{t('addCredentialTitle')}</DialogTitle>
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
                                <Label htmlFor="name">{tc('name')}</Label>
                                <Input
                                    id="name"
                                    name="name"
                                    required
                                    autoFocus
                                    placeholder={t('credNamePlaceholder')}
                                />
                                <InputError message={errors.name} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="description">{tc('description')}</Label>
                                <Textarea
                                    id="description"
                                    name="description"
                                    placeholder={t('descriptionPlaceholder')}
                                />
                                <InputError message={errors.description} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="url">{t('urlOptional')}</Label>
                                <Input
                                    id="url"
                                    name="url"
                                    type="url"
                                    placeholder={t('urlPlaceholder')}
                                />
                                <InputError message={errors.url} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="login">{tc('login')}</Label>
                                <Input
                                    id="login"
                                    name="login"
                                    required
                                    autoComplete="off"
                                />
                                <InputError message={errors.login} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="password">{tc('password')}</Label>
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
                                    {tc('additionalInformation')}
                                </Label>
                                <Textarea
                                    id="additional_information"
                                    name="additional_information"
                                    placeholder={t('additionalInformationPlaceholder')}
                                />
                                <InputError message={errors.additional_information} />
                            </div>

                            <DialogFooter>
                                <Button type="button" variant="outline" onClick={() => onOpenChange(false)}>
                                    {tc('cancel')}
                                </Button>
                                <Button type="submit" disabled={processing}>
                                    {tc('save')}
                                </Button>
                            </DialogFooter>
                        </>
                    )}
                </Form>
            </DialogContent>
        </Dialog>
    );
}
