class Character {

    static update(id) {
        return axios.put('/char', {
            id: id
        });
    }
}

export default Character;
