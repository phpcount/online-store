export function getUrlViewProduct(viewUrl, productId) {
    return (
        window.location.protocol + 
        '//' +
        window.location.host +
        viewUrl + 
        '/' +
        productId
    )
}

export function getUrlProductsByCategory(url, categoryId, page = 1, limit = 30) {
    return (
            url + '?category=/api/categories/' + categoryId
            + '&isPublished=true'
            + '&page=' + page
            + '&itemsPerPage=' + limit
        );
}

export function concatUrlByParams(...params) {
    return params.join('/');
}
