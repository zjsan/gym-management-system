import { computed } from "vue";

//accepting the store and the optional onPageChange callback function to remember the last page number
export function usePagination(store, onPageChange = null) {
    // Map reactive values directly from whatever store is passed in
    const currentPage = computed(() => store.currentPage);
    const lastPage = computed(() => store.lastPage);
    const itemsPerPage = computed(() => store.itemsPerPage);
    const totalItems = computed(() => store.totalItems);
    const isLoading = computed(() => store.loading);
    const error = computed(() => store.errors);

    // Destructure the callback passed by the component
    const { onPageChange } = options;

    // Centralize the execution of a page change
    const executePageChange = (pageNumber) => {

        //  Guard clause: prevent execution if no callback is provided
        if (!onPageChange) {
            console.warn('[usePagination]: No onPageChange callback was provided.');
            return;
        }

        isLoading.value = true;
        error.value = null;
       
        try{
            //update local page state
            currentPage.value = pageNumber

            //Await the callback (handles both sync and async actions seamlessly)
            await onPageChange(pageNumber);
        }
        catch (err) {
            // 4. Catch and handle errors gracefully
            error.value = err?.message || 'Failed to fetch page data.';
            console.error('[usePagination Error]:', err);
        } finally {
            isLoading.value = false;
        }

    };

    // Core Navigation Actions
    const prevPage = () => {
        if (isLoading.value) return;
        if (currentPage.value > 1) {
            executePageChange(currentPage.value - 1);
        }
    };

    const nextPage = () => {
        if (isLoading.value) return;
        if (currentPage.value < lastPage.value) {
            executePageChange(currentPage.value + 1);
        }
    };

    const goToPage = (page) => {
        if (isLoading.value) return;
        const pageNumber = Number(page);
        if (
            pageNumber >= 1 &&
            pageNumber <= lastPage.value &&
            pageNumber !== currentPage.value
        ) {
            executePageChange(pageNumber);
        }
    };

    // Sliding Window Pagination Logic
    const visiblePages = computed(() => {
        const current = currentPage.value;
        const last = lastPage.value;
        const delta = 2; // Easily tweakable block size

        let start = Math.max(1, current - delta);
        let end = Math.min(last, current + delta);

        // Extra protection to ensure consistent width when clicking boundaries
        if (current - start < delta) {
            end = Math.min(last, end + (delta - (current - start)));
        }
        if (end - current < delta) {
            start = Math.max(1, start - (delta - (end - current)));
        }

        const range = [];
        for (let i = start; i <= end; i++) {
            range.push(i);
        }
        return range;
    });

    // Item Range Counter Display Calculations
    const rangeStart = computed(() => {
        if (totalItems.value === 0) return 0;
        return (currentPage.value - 1) * itemsPerPage.value + 1;
    });

    const rangeEnd = computed(() => {
        const end = currentPage.value * itemsPerPage.value;
        return end > totalItems.value ? totalItems.value : end;
    });

    // Expose variables and methods cleanly to the components
    return {
        currentPage,
        lastPage,
        totalItems,
        isLoading,
        visiblePages,
        rangeStart,
        rangeEnd,
        prevPage,
        nextPage,
        goToPage,
    };
}