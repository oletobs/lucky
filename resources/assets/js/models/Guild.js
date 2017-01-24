class Guild {

    static all() {
        return axios.get('/guilds');
    }

    static get(region,server,name) {
        return axios.get('/guilds/' + region + '/' + server + '/' + name);
    }

    static getBlizz(region,server,name) {
        return axios.post('/guilds', {
            region: region,
            server: server,
            name: name.replace(/-/g, ' ')
        });
    }

    static update(region,server,name) {
        return axios.put('/guilds', {
            region: region,
            server: server,
            name: name.replace(/-/g, ' ')
        });
    }
}

export default Guild;
