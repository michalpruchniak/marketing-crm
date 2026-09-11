import { useEcho } from '@laravel/echo-react';
import { useState } from 'react';
import type { DependencyList } from 'react';

type Identifiable = {
    id: string | number;
};

type RealtimeEvent = {
    channel: string;
    event: string;
    resourceKey: string;
};

type RealtimeConfig<T> = {
    created?: RealtimeEvent;
    updated?: RealtimeEvent;
    deleted?: RealtimeEvent;
    shouldInclude?: (item: T) => boolean;
    dependencies?: DependencyList;
};

type RealtimePayload<T> = Record<string, T>;

type DeletedPayload = Record<
    string,
    {
        id: string | number;
    }
>;

export function useRealtime<T extends Identifiable>(
    initialValue: T,
    config: RealtimeConfig<T>,
): T;

export function useRealtime<T extends Identifiable>(
    initialValue: T[],
    config: RealtimeConfig<T>,
): T[];

export function useRealtime<T extends Identifiable>(
    initialValue: T | T[],
    config: RealtimeConfig<T>,
): T | T[] {
    const [value, setValue] = useState(initialValue);
    const [previousInitialValue, setPreviousInitialValue] =
        useState(initialValue);
    const dependencies = config.dependencies ?? [];

    if (initialValue !== previousInitialValue) {
        setPreviousInitialValue(initialValue);
        setValue(initialValue);
    }

    const isList = Array.isArray(initialValue);

    useEcho<RealtimePayload<T>>(
        config.created?.channel ?? '',
        config.created?.event ?? '',
        (payload) => {
            if (!isList || !config.created) {
                return;
            }

            const item = payload[config.created.resourceKey];

            if (config.shouldInclude && !config.shouldInclude(item)) {
                return;
            }

            setValue((current) => {
                if (!Array.isArray(current)) {
                    return current;
                }

                if (current.some((existing) => existing.id === item.id)) {
                    return current;
                }

                return [item, ...current];
            });
        },
        dependencies,
    );

    useEcho<RealtimePayload<T>>(
        config.updated?.channel ?? '',
        config.updated?.event ?? '',
        (payload) => {
            if (!config.updated) {
                return;
            }

            const item = payload[config.updated.resourceKey];

            setValue((current) => {
                if (!Array.isArray(current)) {
                    if (current.id !== item.id) {
                        return current;
                    }

                    return item;
                }

                const exists = current.some(
                    (existing) => existing.id === item.id,
                );
                const shouldInclude =
                    !config.shouldInclude || config.shouldInclude(item);

                if (!shouldInclude) {
                    return exists
                        ? current.filter((existing) => existing.id !== item.id)
                        : current;
                }

                if (!exists) {
                    return [item, ...current];
                }

                return current.map((existing) =>
                    existing.id === item.id ? item : existing,
                );
            });
        },
        dependencies,
    );

    useEcho<DeletedPayload>(
        config.deleted?.channel ?? '',
        config.deleted?.event ?? '',
        (payload) => {
            if (!isList || !config.deleted) {
                return;
            }

            const deletedItem = payload[config.deleted.resourceKey];

            setValue((current) => {
                if (!Array.isArray(current)) {
                    return current;
                }

                return current.filter((item) => item.id !== deletedItem.id);
            });
        },
        dependencies,
    );

    return value;
}
