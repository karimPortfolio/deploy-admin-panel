import { ref } from "vue";
import { useRoute, useRouter } from "vue-router";

export function useFilterHandler(filters, form, onSyncing) {
    const route = useRoute();
    const router = useRouter();
    const search = ref("");

    function buildQuery() {
        const filterObj = {};
        for (const filter of filters.value) {
            if (filter.type === "range") {
                if (form[`${filter.name}_min`] != null)
                    filterObj[`${filter.name}_min`] =
                        form[`${filter.name}_min`];
                if (form[`${filter.name}_max`] != null)
                    filterObj[`${filter.name}_max`] =
                        form[`${filter.name}_max`];
            } else {
                if (form[filter.name] != null && form[filter.name] !== "")
                    filterObj[filter.name] = form[filter.name];
            }
        }

        return {
            search: search.value || undefined,
            filter: filterObj,
        };
    }

    function sync() {
        const query = buildQuery();

        //start with a clean copy of current query and remove existing filter[...] keys
        const newQuery = Object.fromEntries(
            Object.entries(route.query).filter(
                ([key]) => !key.startsWith("filter[")
            )
        );

        //add updated filter and search values
        Object.assign(newQuery, {
            search: query.search || undefined,
            ...Object.fromEntries(
                Object.entries(query.filter).map(([key, value]) => [
                    `filter[${key}]`,
                    value,
                ])
            ),
        });

        //remove empty values
        for (const key in newQuery) {
            if (
                newQuery[key] === undefined ||
                newQuery[key] === "" ||
                newQuery[key] === null
            ) {
                delete newQuery[key];
            }
        }

        router.replace({ query: newQuery });

        if (onSyncing) onSyncing(query.filter);
    }

    function initializeFromQuery() {
        const query = route.query;
        search.value = query.search || "";

        for (const filter of filters.value) {
            if (filter.type === "range") {
                form[`${filter.name}_min`] =
                    query[`filter[${filter.name}_min]`] ?? null;
                form[`${filter.name}_max`] =
                    query[`filter[${filter.name}_max]`] ?? null;
            } else {
                form[filter.name] = query[`filter[${filter.name}]`] ?? null;
            }
        }

        sync();
    }

    function clearFilters() {
        search.value = "";
        for (const filter of filters.value) {
            if (filter.type === "range") {
                form[`${filter.name}_min`] = null;
                form[`${filter.name}_max`] = null;
            } else {
                form[filter.name] = null;
            }
        }
        sync();
    }

    return {
        clearFilters,
        initializeFromQuery,
        sync,
        search,
    };
}
