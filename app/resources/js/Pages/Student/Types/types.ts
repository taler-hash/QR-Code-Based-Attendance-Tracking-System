export interface SortTypes {
    sortOrder: number|null,
    sortField: string
}

export interface PageTypes {
    page: number,
    rows: number
}

export interface UsePageTypes {
    props: {
        data: {}
    }
}

export interface FilterTypes {
    page: number,
    sortBy: string,
    sortType: number|null,
    rows: number,
    filter: any
    section: number
}

export interface FormTypes {
    name?: string,
    id?:number,
    username?: string,
    password?: string|null,
    first_name: string,
    last_name: string,
    sections: {id: number, section: string}[],
    status: string
}

export interface sectionOptionTypes {
    section: number,
    sectionDetails: { section: string}
}
