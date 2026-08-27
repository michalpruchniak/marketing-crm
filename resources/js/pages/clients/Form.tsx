import { Link } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { index as clientsIndex } from '@/routes/clients';
import FieldLabel from './components/FieldLabel';

type FormErrors = Partial<
    Record<'name' | 'email' | 'phone' | 'notes' | 'coordinator_id', string>
>;

type CoordinatorOption = {
    id: number;
    name: string;
};

type Props = {
    processing: boolean;
    errors: FormErrors;
    canAssignCoordinator?: boolean;
    coordinators?: CoordinatorOption[];
};

export default function CreateClientForm({
    processing,
    errors,
    canAssignCoordinator = false,
    coordinators = [],
}: Props) {
    const { t } = useTranslation();

    return (
        <>
            <div className="grid gap-2">
                <FieldLabel htmlFor="name" required>
                    {t('common.name')}
                </FieldLabel>
                <Input
                    id="name"
                    name="name"
                    required
                    autoFocus
                    placeholder={t('clients.namePlaceholder')}
                />
                <InputError message={errors.name} />
            </div>

            <div className="grid gap-2">
                <FieldLabel htmlFor="email">{t('common.email')}</FieldLabel>
                <Input
                    id="email"
                    type="email"
                    name="email"
                    placeholder={t('clients.emailPlaceholder')}
                />
                <InputError message={errors.email} />
            </div>

            <div className="grid gap-2">
                <FieldLabel htmlFor="phone">{t('common.phone')}</FieldLabel>
                <Input
                    id="phone"
                    name="phone"
                    placeholder={t('clients.phonePlaceholder')}
                />
                <InputError message={errors.phone} />
            </div>

            {canAssignCoordinator && (
                <div className="grid gap-2">
                    <FieldLabel htmlFor="coordinator_id" required>
                        {t('clients.coordinator')}
                    </FieldLabel>
                    <select
                        id="coordinator_id"
                        name="coordinator_id"
                        required
                        defaultValue=""
                        className="border-input bg-transparent focus-visible:border-ring focus-visible:ring-ring/50 flex h-9 w-full rounded-md border px-3 py-1 text-sm shadow-xs outline-none focus-visible:ring-[3px]"
                    >
                        <option value="" disabled>
                            {t('clients.coordinatorPlaceholder')}
                        </option>
                        {coordinators.map((coordinator) => (
                            <option
                                key={coordinator.id}
                                value={coordinator.id}
                            >
                                {coordinator.name}
                            </option>
                        ))}
                    </select>
                    <InputError message={errors.coordinator_id} />
                </div>
            )}

            <div className="grid gap-2">
                <FieldLabel htmlFor="notes">{t('common.notes')}</FieldLabel>
                <Textarea
                    id="notes"
                    name="notes"
                    placeholder={t('clients.notesPlaceholder')}
                />
                <InputError message={errors.notes} />
            </div>

            <div className="flex gap-3">
                <Button type="submit" disabled={processing}>
                    {t('clients.saveClient')}
                </Button>
                <Button variant="outline" asChild>
                    <Link href={clientsIndex()}>{t('common.cancel')}</Link>
                </Button>
            </div>
        </>
    );
}
