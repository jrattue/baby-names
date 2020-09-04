import React from 'react';
import {XYPlot, XAxis, YAxis, HorizontalGridLines, LineSeries} from 'react-vis';
import RandomColor from 'randomcolor';

export default class App extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            names: [],
            term: "",
            searchOptions: [],
            loading: false
        }
        this.doSearch = this.doSearch.bind(this);
    }

    addName(nameId){
        fetch(`/api/details/${nameId}`)
            .then(response => response.json())
            .then(data => {
                data.colour = this.getRandomColor(data.id);
                let names = [...this.state.names];
                names.push(data);

                this.setState({
                    'names': names,
                    'searchOptions': [],
                    'term': ''
                })
            })
    }

    removeName(nameId){

        console.log(nameId);
        console.log(this.state.names);

        const names = this.state.names.filter((name) => {
            console.log(name);
            return !(name.id === nameId);
        })

        this.setState({
            'names': names
        });
    }

    doSearch(event){
        const term = event.target.value;
        this.setState({
            'term': term
        })

        fetch(`/api/search/${term}`)
            .then(response => response.json())
            .then(data => {
                this.setState({
                    'searchOptions': data
                })
            })
    }

    getRandomColor(seed) {
        return RandomColor({
            luminosity: 'dark'
        })
    }

//     componentDidMount() { //add listener to do thing
//         this.updateWindowDimensions();
//         window.addEventListener('resize', this.updateWindowDimensions);
//     }
//
//     componentWillUnmount() { //remove event listener after done doing thing
//         window.removeEventListener('resize', this.updateWindowDimensions);
//     }
//
//     updateWindowDimensions = () => {
//         this.setState({ width: window.innerWidth });
//     };
//
// // then in render() (I'm using Sunburst from react-vis):
// <Sunburst
// height={this.state.width / 4}
// width={this.state.width / 4}
// />
// // using width for both dimensions because this component is a circle.

    render(){

        let maxRank = 0;
        let minRank = 1;
        const lines = this.state.names.map(name => {

            const data = name.years.map(year => {
                if(year.rank > maxRank) maxRank = year.rank;
                if(year.rank < minRank) minRank = year.rank;
                return {x: year.year, y: year.rank}
            })

            return (
                <LineSeries
                    data={data}
                    color={name.colour}
                />
            );
        })

        return (


            <div className="row">
                <div className="col-12 col-md-3">
                    <div>
                        <input type="text" name="term" onChange={this.doSearch} value={this.state.term} placeholder="Search for name" autoComplete="off" />
                        {this.state.searchOptions.length > 0 &&
                            <div className="autocomplete-options">
                                <ul>
                                    {this.state.searchOptions.map(item => {
                                        return (<li onClick={() => this.addName(item.id)} key={item.id}>{item.name} <small>({item.gender})</small></li>);
                                    })}
                                </ul>
                            </div>
                        }
                    </div>

                    {this.state.names.length > 0 &&
                        <div>
                            <ul>
                                {this.state.names.map(name => {
                                    return (
                                        <li key={name.id} style={{color: name.colour}}>{ name.name } <small>({ name.gender })</small> - <a onClick={() => this.removeName(name.id)}>x</a>
                                        </li>
                                    )
                                })}
                            </ul>
                        </div>
                    }
                </div>

                <div className="col-12 col-md-9">
                    <XYPlot
                        width={700}
                        height={500}
                        yDomain={[maxRank, minRank]}
                    >
                        <HorizontalGridLines />
                        { lines }
                        <XAxis
                            title="Year"
                        />
                        <YAxis
                            title="Rank"
                        />
                    </XYPlot>
                </div>

            </div>
        )
    }
}