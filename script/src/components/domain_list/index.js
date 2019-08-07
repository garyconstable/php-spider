import React, {Component} from 'react';

import ReactDOM from 'react-dom';

'use strict';

const divStyle = {
    'maxHeight': 'calc(50vh - 40px)',
    'overflow': 'scroll'
};

class DomainList extends Component {

    constructor(props) {
        super(props);

        this.state = {
            data: [],
            page: 1,
            total_pages : 1
        };

        this.decreasePage = this.decreasePage.bind(this);
        this.increasePage = this.increasePage.bind(this);
        this.getPagedData = this.getPagedData.bind(this);
    }

    componentDidMount() {
        this.getPagedData(1);
    }

    getPagedData(pageNumber){

        fetch('/api/domain/list?page=' + parseInt(pageNumber))
            .then(response=> response.json())
            .then(json => {
                if(json.data){

                    console.log(json)

                    this.setState({
                        data: json.data,
                        page: pageNumber,
                        total_pages: json.total_pages
                    })
                }
            });
    }

    decreasePage(){
        if( this.state.page > 1 ){
            this.getPagedData(this.state.page -= 1);
        }
    }

    increasePage(){
        if( this.state.page < this.state.total_pages ) {
            this.getPagedData(this.state.page += 1);
        }
    }

    render() {
        return (
            <div>
                <div style={divStyle}>
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
                </div>
                <div className="mt-3">
                    <button className="btn btn-default btn-50" onClick={this.decreasePage}>Previous</button>
                    <button className="btn btn-default btn-50" onClick={this.increasePage}>Next</button>
                </div>
            </div>
        );
    }
}

ReactDOM.render(<DomainList/>, document.querySelector('[data-app="list"]'));

export default DomainList;