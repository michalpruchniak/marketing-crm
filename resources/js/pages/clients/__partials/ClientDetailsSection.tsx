import { useTranslation } from 'react-i18next';
import type { Client } from '../types';

type Props = {
    client: Client;
};

export default function ClientDetailsSection({ client }: Props) {
    const { t } = useTranslation();

    return (
        <section className="grid max-w-3xl gap-3 rounded-xl border p-4 text-sm">
            <div>
                <span className="text-muted-foreground">{t('common.email')}: </span>
                {client.email ?? '—'}
            </div>
            <div>
                <span className="text-muted-foreground">{t('common.phone')}: </span>
                {client.phone ?? '—'}
            </div>
            <div>
                <span className="text-muted-foreground">{t('common.notes')}: </span>
                {client.notes ?? '—'}
            </div>
        </section>
    );
}
