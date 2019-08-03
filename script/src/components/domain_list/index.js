import React, {Component} from 'react';

import ReactDOM from 'react-dom';

'use strict';

class DomainList extends Component {

    constructor(props) {
        super(props);

        this.state = {
            data: [],
        };
    }

    componentDidMount() {

        console.log('--> mounted');

        fetch('/api/domain/list')
            .then(response=> response.json())
            .then(json => {

                console.log(json)

                if(json.data){
                    this.setState({
                        data: json.data
                    })
                }
            });
    }

    render() {
        return (
            <table className="table table-striped table-dark">
                <thead>
                <tr>
                    <td>#</td>
                </tr>
                </thead>
                <tbody>
                    {this.state.data.map(function(item, index){
                        return <tr key={index}>
                            <td><a href={item.url} target="_blank">{item.url}</a></td>
                        </tr>;
                    })}
                </tbody>
            </table>
        );
    }
}

ReactDOM.render(<DomainList/>, document.querySelector('[data-app="list"]'));

export default DomainList;