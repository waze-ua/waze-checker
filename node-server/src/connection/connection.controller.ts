import { Controller, Get, Param } from '@nestjs/common';
import { ConnectionService } from './connection.service';
import { ConnectionsResponseInterface } from './types/connectionsResponse.interface';

@Controller('connections')
export class ConnectionController {
  constructor(private readonly connectionService: ConnectionService) {}

  @Get()
  async findAll(): Promise<ConnectionsResponseInterface> {
    const connections = await this.connectionService.findAll();

    return this.connectionService.buildResponse(connections);
  }

  @Get(':id')
  async findRecord(
    @Param('id') id: number,
  ): Promise<ConnectionsResponseInterface> {
    const connection = await this.connectionService.findById(id);

    return this.connectionService.buildResponse(connection);
  }
}
